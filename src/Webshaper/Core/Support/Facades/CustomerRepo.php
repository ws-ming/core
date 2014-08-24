<?php namespace Webshaper\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class CustomerRepo extends Facade{
    protected static function getFacadeAccessor(){return 'webshaper-customerRepo';}
} 