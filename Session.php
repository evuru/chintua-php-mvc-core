<?php


namespace app\core;


class Session{
    protected const FLASH_KEY='flash_messages';
    public function __construct(){
        session_start();
//        session_destroy();
        $flashMessages = $_SESSION[self::FLASH_KEY]??[];
        foreach ($flashMessages as $key=>&$flashMessage){
            //mark to be removed;
            $flashMessage['remove']=true;
        }
        $_SESSION[self::FLASH_KEY]=$flashMessages;

//        echo "<pre>";
//        var_export($flashMessages);
//        echo "</pre>";
//        exit;

    }

    public function setFlash($key, $message){
        $_SESSION[self::FLASH_KEY][$key]=['remove'=>false,'value'=>$message];
    }

    public function getFlash($key){
       return $_SESSION['flash_messages'][$key]['value']??'';
        //unset($message);
    }

    public function set($key,$value){
        $_SESSION[$key]=$value;
    }
    public function get($key){
        return $_SESSION[$key]??false;
    }
    public function remove($key){
        unset($_SESSION[$key]);
    }

    public function __destruct(){
        // iterate over marked to be removed session flash messages; and remove them;
        $flashMessages = $_SESSION[self::FLASH_KEY]??[];
        foreach ($flashMessages as $key=>&$flashMessage){
            //mark to be removed;
            if($flashMessage['remove']===true){
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY]=$flashMessages;

    }

}