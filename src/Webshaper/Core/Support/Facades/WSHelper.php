<?php namespace Webshaper\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class WSHelper extends Facade{

    protected static function getFacadeAccessor(){
        return 'webshaper-wshelper';
    }
} 