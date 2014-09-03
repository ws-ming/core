<?php namespace Webshaper\Core\Models;

class Product extends BaseModel {
	protected $table = "product1";
    protected $primaryKey = "intPKProduct";
	protected $fillable = [];
    protected $connection = 'webshaper-tenant';

    public function productItem(){
        return $this->belongsTo('Webshaper\Core\Models\ProductItem','intPKProductItem');
    }

    public function productItemBasic(){
        $columns = array(
            'intPKProductItem',
            'txtProdItemName',
            'txtProdItemShortDesc',
            'txtPicSmallURL',
            'intPKCategory'
        );
        return $this->productItem()->select($columns);
    }

    public function galleries(){
        return $this->hasMany('Webshaper\Core\Models\ProductGallery','productId');
    }

    public function productItemGallery(){
        return $this->hasMany('Webshaper\Core\Models\ProductGallery','pkProductItem','intPKProductItem')->orderBy('defaultPic','desc');
    }

}