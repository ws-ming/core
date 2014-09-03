<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\OrderItem;

class OrderItemRepository extends BaseRepository{

    function __construct(OrderItem $model){

        parent::__construct($model);
    }

    public function addOrderItems($orderId, array $items){
        foreach($items as $id => $qty){
            $orderItems = new OrderItem();
            $item = \ProductRepo::get($id);

            $orderItems->intPKOrder = $orderId;
            $orderItems->fUnitPrice = $item->fPrice;
            $orderItems->intPKParent = $item->intPKParent;
            $orderItems->intPKProduct = $item->intPKProduct;
            $orderItems->intQty = $qty;
            $orderItems->txtProductName = $item->txtProdName;
            $orderItems->txtSKU = $item->txtSKU;

            $orderItems->save();
        }
    }
}