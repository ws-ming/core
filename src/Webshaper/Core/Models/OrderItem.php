<?php namespace Webshaper\Core\Models;


class OrderItem extends BaseModel{

    protected $table = "orderitems";
    protected $primaryKey = "intPKOrderItems";
    protected $fillable=[];


    public function order(){
        return $this->belongsTo('Webshaper\Core\Models\Order','intPKOrder');
    }
}