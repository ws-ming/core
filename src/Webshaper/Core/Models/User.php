<?php namespace Webshaper\Core\Models;


class User extends BaseModel{
    protected $table = "user";
    protected $primaryKey = "txtLoginID";
    protected $fillable=[];
}