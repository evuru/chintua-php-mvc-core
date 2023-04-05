<?php


namespace app\core\middleWares;


use app\core\Application;
use app\core\exception\ForbiddenException;


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