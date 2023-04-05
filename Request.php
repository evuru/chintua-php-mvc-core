<?php


namespace app\core;


class Request{
    public function getPath(){
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path,'?');
        return $position===false?$path:substr($path,0,$position);
//        echo $position;

    }
    public function method(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    public function isGET(){
        return $this->method()=== 'get';
    }
    public function isPost(){
        return $this->method()=== 'post';
    }
    public function getBody(){
        $body = [];

        if ($this->method()==="get"){
            foreach($_GET as $key=>$value){
                $body[$key] =  filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        if ($this->method()==="post"){
            foreach($_POST as $key=>$value){
                $body[$key] =  filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        return $body;
    }

}


/*the get path method returns the name of the page that the request_method is targeting
even if parameters are passed in the URL=> like ?id=22&a=33 the return statement filters this*/
