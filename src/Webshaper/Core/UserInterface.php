<?php
namespace Webshaper\Core;

interface UserInterface
{
    /**
     * @param $loginID user login id
     * @param $password plain password
     * @return bool $status
     */
    public function authenticate($loginID, $password,$type);
}