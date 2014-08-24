<?php namespace Webshaper\Core\Exception;

class BaseException extends \Exception{

    protected  $errorMessage;

    public function __construct($errorCode){
        $this->errorMessage = $errorCode;
    }

    public function getErrorJson(){
        $json = array('error'=>'true',
            'message'=>$this->errorMessage);

        return $json;
    }
} 