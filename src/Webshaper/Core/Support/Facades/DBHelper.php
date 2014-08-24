<?php

namespace Webshaper\Core\Support\Facades;

use Illuminate\Support\Facades\Facade;
class DBHelper extends Facade{
    protected static function getFacadeAccessor(){return 'webshaper-dbhelper';}
}