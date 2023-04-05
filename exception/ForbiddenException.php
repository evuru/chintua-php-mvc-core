<?php


namespace evuru\chintuaphpmvc\exception;


class ForbiddenException extends \Exception{

    protected $message = "You don't have Permissions to acces this page";
    protected $code = 403;

}