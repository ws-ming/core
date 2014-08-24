<?php namespace Webshaper\Core\Exception;

class WSException extends \Exception{


    public function __construct($errorCode){
        parent::__construct($errorCode);
    }

} 