<?php
namespace SoftUni;


class Application
{
    private $controllerName;
    private $actionName;

    private $controller;

    public function start()
    {
        \Softuni\Router::readAllRoutes();

        $uri = Router::make_uri();
        $params = Router::match_uri($uri);
        //var_dump($params);
        if ($params)
        {
            $controller = ucwords($params['controller']);
            $this->actionName = $params['action'];

            unset($params['controller'], $params['action']);

            $this->controllerName = $controller;

            $classes = get_declared_classes();
            $pattern = '/.*\\\\'.$this->controllerName.'$/';
            //var_dump($pattern);
            $filteredClasses = array_filter($classes, function ($class) use ($pattern) {
                if (preg_match($pattern, $class, $match)) {
                   return $class;
               }
            });

            //var_dump($filteredClasses);

            if ($filteredClasses) {
                foreach ($filteredClasses as $filteredClass) {
                    //var_dump($filteredClass);
                    if (method_exists($filteredClass, $this->actionName)) {
                        if (preg_match('/Areas\\\\(.*?)\\\\/', $filteredClass, $match)) {
                            View::$area = $match[1];
                        }

                        $this->controller = new $filteredClass;
                        View::$controllerName = $this->controllerName;
                        View::$actionName = $this->actionName;
                        call_user_func_array(array($this->controller, $this->actionName), $params);
                    } else {
                        throw new \Exception("Method not found");
                    }
                }
            }
            else
            {
                throw new \Exception("Controller not found");
            }
        }
        else
        {
            throw new \Exception("Route not found");
        }
    }
}