<?php
namespace SoftUni;


class Application
{
    private $controllerName;
    private $actionName;

    private $controller;

    const CONTROLLERS_NAMESPACE = 'SoftUni\\Controllers\\';
    const CONTROLLERS_SUFFIX = 'Controller';

    public function start()
    {
        \Softuni\Router::readAllRoutes();

        $uri = Router::make_uri();
        $params = Router::match_uri($uri);
        var_dump($params);
        if ($params)
        {
            $controller = ucwords($params['controller']);
            $this->actionName = $params['action'];

            unset($params['controller'], $params['action']);

            $this->controllerName =
                self::CONTROLLERS_NAMESPACE
                . $controller
                . self::CONTROLLERS_SUFFIX;

            echo "test classs";
            var_dump($this->controllerName);
            $classes = get_declared_classes();
            print_r($this->controllerName);
            print_r($classes);
            print_r(in_array($this->controllerName, array_values($classes)));
           // if (class_exists($this->controllerName))
          //  {
                if (method_exists($this->controllerName, $this->actionName))
                {
                    $this->controller = new $this->controllerName();
                    call_user_func_array(array($this->controller, $this->actionName), $params);
                    View::$controllerName = $this->controllerName;
                    View::$actionName = $this->actionName;
                }
                else
                {
                    trigger_error("Method not found", E_USER_ERROR);
                }
            }
            //else
           // {
                trigger_error("Controller not found", E_USER_ERROR);
            //}
       // }
        //else
        //{
        //    trigger_error("Route not found", E_USER_ERROR);
       // }
    }
}