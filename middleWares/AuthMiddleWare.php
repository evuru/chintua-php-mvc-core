<?php


namespace evuru\chintuaphpmvc\middleWares;


use evuru\chintuaphpmvc\Application;
use evuru\chintuaphpmvc\exception\ForbiddenException;


class AuthMiddleWare extends BaseMiddleWare {
    public array $actions;
    public function __construct(array $actions=[]){
        $this->actions = $actions;
    }

    public function execute(){
        if(Application::$app::isGuest()){
            if(empty($this->actions)||in_array(Application::$app->action,$this->actions)){
                throw new ForbiddenException();
            }
        }

    }
}