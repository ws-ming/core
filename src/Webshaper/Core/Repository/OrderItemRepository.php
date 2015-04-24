<?php namespace Webshaper\Core\Repository;

use Carbon\Carbon;
use Webshaper\Core\Models\OrderItem;
use Webshaper\Core\Models\Product;

class OrderItemRepository extends BaseRepository{

    function __construct(OrderItem $model){

        parent::__construct($model);
    }

    public function addOrderItems($orderId, array $orderItems){

        foreach($orderItems as $orderItem){
            $product = Product::find($orderItem['product_id']);
            $product->intStockQty = $product->intStockQty - $orderItem['quantity'];
            $product->dtUpdated = Carbon::now();
            $product->save();

            $orderItems = new OrderItem();
            $item = \ProductRepo::get($orderItem['product_id']);
            $orderItems->intPKOrder = $orderId;
            $orderItems->fUnitPrice = $item->fPrice;
            $orderItems->intPKParent = $item->intPKParent;
            $orderItems->intPKProduct = $item->intPKProduct;
            $orderItems->intQty = $orderItem['quantity'];
            $orderItems->txtProductName = $item->txtProdName;
            $orderItems->txtSKU = $item->txtSKU;
            $orderItems->taxrate = $orderItem['taxRate'];
            $orderItems->save();
        }

    }

    public function returnOrderItems($orderItems)
    {
        foreach($orderItems as $item)
        {
            $product = Product::find($item->intPKProduct);

            $product->intStockQty = $product->intStockQty + $item->intQty;

            $product->dtUpdated = Carbon::now();

            $product->save();
        }
    }
}