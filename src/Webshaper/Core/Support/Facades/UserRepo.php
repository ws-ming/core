<?php namespace Webshaper\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;

class UserRepo extends Facade{

    protected static function getFacadeAccessor(){return 'webshaper-userrepo';}
} 