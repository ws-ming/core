<?php namespace Webshaper\Core\Models;

class Product extends BaseModel {
	protected $table = "product1";
    protected $primaryKey = "intPKProduct";
	protected $fillable = [];
    protected $connection = 'webshaper-tenant';

    public function productItem(){
        return $this->belongsTo('Webshaper\Core\Models\ProductItem','intPKProductItem');
    }

}