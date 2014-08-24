<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\ProductInterface;
use Webshaper\Core\Models\Product as Product;
class ProductRepository extends BaseRepository implements ProductInterface{

    function __construct(Product $model){

        parent::__construct($model);
    }

    /**
     * @param $intPkProduct
     * @return mixed
     */
    public function getProductItem($intPkProduct)
    {
        return $this->model->find($intPkProduct)->productItem;
    }

    /**
     * get product using the SKU
     * @param $sku
     */
    public function getBySKU($sku){
//        dd(\Config::get('database.connections.test'));
        return $this->model->where('txtSku','like','%'.$sku.'%')->get();
    }
}