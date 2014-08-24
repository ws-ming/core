<?php namespace Webshaper\Core\Models;

abstract class BaseModel extends \Eloquent{

    protected $connection = 'webshaper-tenant';
    protected $timestamps = false;
}