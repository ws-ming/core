<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\ProductItemInterface;
use Webshaper\Core\Models\ProductItem as ProductItem;

class ProductItemRepository extends BaseRepository implements ProductItemInterface {

    function __construct(ProductItem $model){

        parent::__construct($model);
    }

    public function getProducts($intPKProduct)
    {
        return $this->model->find($intPKProduct)->products;
    }


}