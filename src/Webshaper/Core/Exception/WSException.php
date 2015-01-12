<?php namespace Webshaper\Core\Exception;

class WSException extends BaseException{


    public function __construct($errorCode){
        parent::__construct($errorCode);
    }


} 