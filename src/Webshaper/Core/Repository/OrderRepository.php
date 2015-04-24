<?php namespace Webshaper\Core\Repository;

use Carbon\Carbon;
use Webshaper\Core\Exception\WSException;
use Webshaper\Core\Models\Order;
use Webshaper\Core\Support\ErrorCode;
use Webshaper\Core\Support\Facades\WSHelper;

class OrderRepository extends BaseRepository{


    /**
     * @var OrderItemRepository
     */
    private $orderItemRepo;
    /**
     * @var ProductRepository
     */
    private $productRepo;
    /**
     * @var OrderLogRepository
     */
    private $orderLogRepo;

    function __construct(Order $model, OrderItemRepository $orderItemRepo, OrderLogRepository $orderLogRepo, ProductRepository $productRepo){

        parent::__construct($model);
        $this->orderItemRepo = $orderItemRepo;
        $this->productRepo = $productRepo;
        $this->orderLogRepo = $orderLogRepo;
    }

    public function getOrderItems($intPKOrder){
        $result = $this->model->with('orderItems')->find($intPKOrder);
        if(is_null($result)) throw new WSDataNotFound();

        return $result;
    }

    public function createNewOrder($total,$taxTotal,$taxableAmount,$taxMode, $currency, $orderStatus, $paymentMethod,array $items, $customerId, $orderType,$calculatedDiscount = 0,$discountType = null,$ignoreStock = false,$jsonData = null,$remarks = null,$useCustAddr =  true, $shipAddrSameWithCust = true, $billAddrSameWithCust = true){
        //order type 1=store 2=pos
        //check stock
        $stock = $this->productRepo->isStockSufficient($items);

        if(!$ignoreStock && $stock !== true )
        {
            $msg = array(
                'type' =>   ErrorCode::PRODUCT_QUANTITY_INSUFFICIENT,
                'stock' => $stock
            );
            throw new WSException($msg);
        }

        \DB::connection('webshaper-tenant')->beginTransaction();

        try{
            //create a new order
            $order = new Order();
            $order->fTotal = $total;
            $order->intQty = count($items);
            $order->txtOrderCurr = $currency;
            $order->txtOrderStatus = $orderStatus;
            $order->txtPaymentMethod = $paymentMethod;
            $order->intOrderType = $orderType;
            $order->taxableAmount = $taxableAmount;
            $order->taxes = $taxTotal;
            $order->taxPriceMode = $taxMode;
            $order->dtCreated = Carbon::now();

//        if(!is_null($discountType)){ only for discount code
//
//            $order->fDiscountCalculated = $calculatedDiscount;
//
//        }

            if($discountType == "FLAT")
            {
                $order->fDiscountValue = $calculatedDiscount;

            }else if($discountType == "PERCENTAGE")
            {
                $order->fDiscountPercentage = $calculatedDiscount;
            }

            if(!is_null($jsonData))
            {
                $order->jsonData = $jsonData;
            }

            if(!is_null($remarks))
            {
                $order->txtRemark1 = $remarks;
            }

            $order->save();

            //add the order items
            $this->orderItemRepo->addOrderItems($order->intPKOrder,$items);

            //if customer id given and exists,calculate the points and assign to customer
            $order->txtOrderRef = "P".str_pad($order->intPKOrder,9,0,STR_PAD_LEFT);

            $order->save();

//            $this->orderLogRepo->insertLog($order->intPKOrder,$username,"Order cancelled. Status CANCELLED.");



        }catch(\Exception $e){
            \DB::connection('webshaper-tenant')->rollback();
            throw $e;
        }

        \DB::connection('webshaper-tenant')->commit();

        return $order;
    }

    public function createNewOrderForGuest($total,$taxTotal,$taxableAmount,$taxMode, $currency, $orderStatus, $paymentMethod, array $items,$customerId, $orderType,$calculatedDiscount = 0,$discountType = null,$ignoreStock = false,$jsonData = null,$remarks = null){

        return $this->createNewOrder($total,$taxTotal,$taxableAmount,$taxMode,$currency,$orderStatus,$paymentMethod,$items,$customerId, $orderType, $calculatedDiscount,$discountType,$ignoreStock,$jsonData,$remarks);
    }

    public function getTransactionOverview($method,$start = 0, $limit = 0,$column = array('dtCreated','fTotal','intPKOrder','txtOrderRef','txtOrderStatus','txtPaymentMethod'))
    {

        if($limit == 0)
        {
            return $this->model->where('txtPaymentMethod','like',$method)->orderBy('dtCreated','desc')->get($column);
        }else
        {
            return $this->model->where('txtPaymentMethod','like',$method)->skip($start)->limit($limit)->orderBy('dtCreated','desc')->get($column);
        }
    }

    public function getTransactionDetails($id)
    {
        return $this->model->with('orderItems')->find($id);
    }

    public function cancelOrder($orderId,$username)
    {
        //return items if is completed or processed

        \DB::connection('webshaper-tenant')->beginTransaction();

        try{
            $order = $this->getTransactionDetails($orderId);

            if($order->txtOrderStatus == "cancelled")
            {
                throw new WSException(ErrorCode::ORDER_ALREADY_CANCELLED);
            }

            //update the order to cancel status
            $prevStatus = $order->txtOrderStatus;

            $order->txtOrderStatus = "cancelled";

            $orderJson = $order->jsonData;

            $orderJson = json_decode($orderJson);

            $orderJson->cancelled_by = $username;

            $order->jsonData = json_encode($orderJson);

            $order->save();

            if(strcasecmp($prevStatus,"processed") || strcasecmp($prevStatus,"completed") )
            {
                $this->orderItemRepo->returnOrderItems($order->orderItems);
            }

            $this->orderLogRepo->insertLog($orderId,$username,"Order cancelled. Status CANCELLED.");


        }catch(\Exception $e)
        {
            \DB::connection('webshaper-tenant')->rollback();
            throw $e;
        }

        \DB::connection('webshaper-tenant')->commit();

        return $order;


        //deduct the customer point
    }

    public function getOrderByTimeRange($from,$to)
    {
        $orders = $this->model->with('orderItems')->where('dtCreated','>=',$from)->where('dtCreated','<=',$to)->get();

        return $orders;
    }

    public function getOrderByTimeRangeWithMethod($from,$to,$method)
    {
        $orders = $this->model->with('orderItems')->where('dtCreated','>=',$from)->where('dtCreated','<=',$to)->where('txtPaymentMethod','like',$method)->get();

        return $orders;
    }
}