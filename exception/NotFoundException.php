<?php


namespace evuru\chintuaphpmvc\exception;


class NotFoundException extends \Exception{

    protected $message = "Page not found";
    protected $code = 404;

}