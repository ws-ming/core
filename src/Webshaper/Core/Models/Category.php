<?php

namespace Webshaper\Core\Models;


class Category extends BaseModel{

    protected $table = "category1" ;
    protected $fillable=[];

    public function parent()
    {
        return $this->belongsTo('Webshaper\Core\Models\Category','intPKParent','intPKCategory');
    }

    public function childs()
    {
        return $this->hasMany('Webshaper\Core\Models\Category','intPKCategory','intPKParent');
    }
}