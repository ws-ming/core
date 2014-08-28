<?php namespace Webshaper\Core\Models;

class ProductItem extends BaseModel {
	protected $table = "productitem";
    protected $primaryKey = "intPKProductItem";
	protected $fillable = [];

    public function products(){
        return $this->hasMany('Webshaper\Core\Models\Product','intPKProductItem');
    }

    public function productsBasic(){
        $columns = array(
            'intPKProduct',
            'intStockQty',
            'txtSKU',
            'txtStockStatus',
            'txtProdName',
            'fPrice'
        );
        return $this->products()->select($columns);
    }

}