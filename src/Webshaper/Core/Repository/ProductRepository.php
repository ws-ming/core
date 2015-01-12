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

    public function getLastModified()
    {
        $product = $this->model->limit(1)->orderBy('dtUpdated','desc')->with(array('productItemBasicWithGallery'))->first();

        return $product;
    }

    public function isStockSufficient(array $orderItems)
    {
        $stock = null;

        foreach($orderItems as $orderItem)
        {
            $item =  $this->model->find($orderItem['product_id']);
            if($item->intStockQty < $orderItem['quantity'])
            {
                $stock[] = array(
                    'product_id' => $orderItem['product_id'],
                    'product_name' => $item->txtProdName,
                    'stock_quantity' => $item->intStockQty
                );
            }
        }

        if(count($stock) > 0)
        {
            return $stock;
        }

        return true;
    }
}