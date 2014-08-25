<?php namespace Webshaper\Core\Exception;

class WSDataNotFound extends BaseException{

    function __construct($errorCode = 'Data not found'){
        parent::__construct($errorCode);
    }
} 