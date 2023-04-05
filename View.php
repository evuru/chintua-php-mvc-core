<?php


namespace app\core;


class View{
    public string $layout;
    public string $title;

    public function __construct(){
        $this->title=Application::$app->title;
    }

    public function renderView($view,$params=[]){
        $viewContent = $this->renderOnlyView($view,$params);
        $layoutContent = $this->layoutContent();
        return str_replace("{{content}}",$viewContent,$layoutContent);
        include_once(Application::$ROOT_DIR."/views/$view.php");
    }

    protected function layoutContent(){
        $this->layout=$this->layout??Application::$app->layout;
        ob_start();
        include_once(Application::$ROOT_DIR."/views/layouts/".$this->layout.".php");
        return ob_get_clean();
    }

    protected function renderOnlyView($view,$params){
        foreach ($params as $key=>$value){
            $$key = $value;
        }

        ob_start();
        include_once(Application::$ROOT_DIR."/views/$view.php");
        return ob_get_clean();
    }
}