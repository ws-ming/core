<?php namespace Webshaper\Core\Models;

class Order extends BaseModel{

    protected $table = "order1";
    protected $primaryKey = "intPKOrder";
    protected $fillable=[];

    public function orderItems(){
        return $this->hasMany('Webshaper\Core\Models\OrderItem','intPKOrder');
    }
}