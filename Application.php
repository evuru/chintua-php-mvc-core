<?php
namespace app\core;

use app\core\db\Database;
use app\core\db\DatabaseModel;

class Application{
    public static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public string $action = '';
    public string $title = 'Chintua.';

    public Request $request;
    public Router $router;
    public View $view;
    public Response $response;
    public Session $session;
    public Database $db;
    public $writeDB;
    public  $readDB;
    public ?DatabaseModel $user;

    public static Application $app;

    public function __construct($rootPath,array $config){
        $this->userClass = $config['userClass'];
        self::$app = $this;
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request,$this->response);
        $this->view = new View();
        $this->db =  new Database($config['db']);
        $this->writeDB = $this->db::connectWriteDB();
        $this->readDB = $this->db::connectReadDB();


        $primaryValue = $this->session->get('user');
        if ($primaryValue){
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey=>$primaryValue]);
        }else{
            $this->user=null;
        }

    }
    public function run(){
        try {
            echo $this->router->resolve();
        }
        catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView("error",['exception'=>$e]);
        }
    }


    public function login(DatabaseModel $user){
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user',$primaryValue);
        return true;
    }

    public function logout(){
        $this->user=null;
        $this->session->remove('user');
//        $this::$app->response->redirect('/home');
        return true;
    }
    public static function isGuest(){
        return !isset($_SESSION['user']);
    }
}
