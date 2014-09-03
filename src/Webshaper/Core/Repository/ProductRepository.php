<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Exception\WSDataNotFound;
use Webshaper\Core\ProductInterface;
use Webshaper\Core\Models\Product as Product;
class ProductRepository extends BaseRepository{

    function __construct(Product $model){

        parent::__construct($model);
    }

    /**
     * @param $intPkProduct
     * @return mixed
     */
    public function getProductItem($intPkProduct)
    {
        $result = $this->model->with('productItem')->find($intPkProduct);

        if(is_null($result)) throw new WSDataNotFound();
        return $result;
    }

    /**
     * get product using the SKU
     * @param $sku
     */
    public function getBySKU($sku){
        return $this->search($sku,'txtSKU');
    }

    public function getByName($prodItemName){
        return $this->search($prodItemName,'txtProdName');
    }

    public function searchProduct($keywords){
        return $this->search($keywords,array('txtProdName','txtSKU'));
    }

    public function getGallery($intPKProduct){
        $products = $this->model->with(array('galleries','productItemGallery'))->find($intPKProduct);
        return $products;
    }
}