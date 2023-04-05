<?php
namespace app\core;
class Router
{
    public Request $request;
    protected array $routes = [];
    public Response $response;
    public View $view;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
//        $this->layout="main";
    }


    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStatusCode(404);
//           return $this->renderView("404");
            throw new \Exception("Page Not found", 404);
            // return $this->renderContent("404 <br> Not Found");
        }
        if (is_string($callback)) {
            return $this->view->renderView($callback);
        }

        if (is_array($callback)) {
            /** @var \app\core\Controller $controller */
            $callback[0] = new $callback[0]();
            Application::$app->action = $callback[1];
            $controller = $callback[0];

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }

        return call_user_func($callback, $this->request);
    }
}

/*echo "<pre>";
        var_dump($params);
        echo "<pre>";*/