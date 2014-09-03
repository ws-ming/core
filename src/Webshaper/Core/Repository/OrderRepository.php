<?php namespace Webshaper\Core\Repository;

use Carbon\Carbon;
use Webshaper\Core\Models\Order;

class OrderRepository extends BaseRepository{


    /**
     * @var OrderItemRepository
     */
    private $orderItemRepo;

    function __construct(Order $model, OrderItemRepository $orderItemRepo){

        parent::__construct($model);
        $this->orderItemRepo = $orderItemRepo;
    }

    public function getOrderItems($intPKOrder){
        $result = $this->model->with('orderItems')->find($intPKOrder);
        if(is_null($result)) throw new WSDataNotFound();

        return $result;
    }

    public function createNewOrder($total, $itemQty, $currency, $orderStatus, $paymentMethod,array $items, $customerId, $orderType, $useCustAddr =  true, $shipAddrSameWithCust = true, $billAddrSameWithCust = true){
        //order type 1=store 2=pos
        //create a new order
        $order = new Order();
        $order->fTotal = $total;
        $order->intQty = $itemQty;
        $order->txtOrderCurr = $currency;
        $order->txtOrderStatus = $orderStatus;
        $order->txtPaymentMethod = $paymentMethod;
        $order->dtCreated = Carbon::now();

        $order->save();

        //add the order items
        $this->orderItemRepo->addOrderItems($order->intPKOrder,$items);

        //if customer id given and exists,calculate the points and assign to customer
        $order->txtOrderRef = "P".str_pad($order->intPKOrder,9,0,STR_PAD_LEFT);
        $order->save();

        return $order;
    }

    public function createNewOrderForGuest($total, $itemQty, $currency, $orderStatus, $paymentMethod, array $items){

    }

}