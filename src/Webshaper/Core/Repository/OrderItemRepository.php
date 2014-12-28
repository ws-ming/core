<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Exception\WSException;
use Webshaper\Core\Models\OrderItem;
use Webshaper\Core\Support\ErrorCode;

class OrderItemRepository extends BaseRepository{

    function __construct(OrderItem $model){

        parent::__construct($model);
    }

    public function addOrderItems($orderId, array $orderItems){

        foreach($orderItems as $orderItem){
            $orderItems = new OrderItem();
            $item = \ProductRepo::get($orderItem['product_id']);
            $orderItems->intPKOrder = $orderId;
            $orderItems->fUnitPrice = $item->fPrice;
            $orderItems->intPKParent = $item->intPKParent;
            $orderItems->intPKProduct = $item->intPKProduct;
            $orderItems->intQty = $orderItem['quantity'];
            $orderItems->txtProductName = $item->txtProdName;
            $orderItems->txtSKU = $item->txtSKU;

            $orderItems->save();
        }

    }

    public function isStockSufficient(array $orderItems)
    {
        foreach($orderItems as $orderItem)
        {
            $item =  OrderItem::find($orderItem['product_id']);
            if($item->intQty < $orderItem['quantity'])
            {
                return true;
            }
        }

        return false;
    }
}