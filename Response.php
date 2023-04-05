<?php


namespace evuru\chintuaphpmvc;


class Response{
    public function setStatusCode(int $code){
        http_response_code($code);
    }
    public function redirect($location){
        header("location: $location");
    }
}