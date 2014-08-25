<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Exception\WSDataNotFound;
use Webshaper\Core\ProductItemInterface;
use Webshaper\Core\Models\ProductItem as ProductItem;

class ProductItemRepository extends BaseRepository implements ProductItemInterface {

    function __construct(ProductItem $model){

        parent::__construct($model);
    }

    public function getProducts($intPKProductItem)
    {
        $result = $this->model->find($intPKProductItem);
        if(is_null($result)) throw new WSDataNotFound();

        return $result->products;
    }

    public function getByName($prodItemName){

        return $this->search($prodItemName,'txtProdItemName');
    }

}