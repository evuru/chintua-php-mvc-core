<?php


namespace app\core;


use app\core\middleWares\BaseMiddleWare;

class Controller{
    protected array $middlewares = [];

    public function render($view,$params=[]){
        return Application::$app->view->renderView($view,$params);
    }
    public function  setLayout($layout){
        return Application::$app->view->layout=$layout;
    }



    public function registerMiddleWare(BaseMiddleWare $middleWare)
    {
        $this->middlewares[] = $middleWare;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }



}