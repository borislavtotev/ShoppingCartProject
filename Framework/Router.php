<?php

namespace SoftUni;


class Router
{
    private static $uri;
    private static $routes;

    public static function readAllRoutes()
    {
        $currentDir = $_SERVER['DOCUMENT_ROOT'];
        echo $currentDir;
        echo "adaal";
        return 1;
    }

    public static function make_uri()
    {
        if(!empty($_SERVER['PATH_INFO']))
        {
            self::$uri = $_SERVER['PATH_INFO'];
        }
        elseif (!empty($_SERVER['REQUEST_URI']))
        {
            self::$uri = $_SERVER['REQUEST_URI'];

            //removing index
            if (strpos(self::$uri, 'index.php') !== FALSE)
            {
                self::$uri = str_replace('index.php', '', self::$uri);
            }
        }

        return parse_url(trim(self::$uri, '/'), PHP_URL_PATH);
    }

    // returns params[] with controller, action, params
    public static function match_uri($uri)
    {
        require('Config' . DIRECTORY_SEPARATOR . 'RouteConfig.php');

        if (empty($customRoutes))
        {
            trigger_error("Routes must not be empty", E_USER_ERROR);
        }

        self::$routes = $customRoutes;

        // add default route at the end of the array. If config route is not found, the default will be used
        array_push($customRoutes, $defaultRoute);

        $params = array();

        foreach ($customRoutes as $route)
        {
            //we keep our route uri in the [0] position
            $route_uri = array_shift($route);

            if (!preg_match($route_uri, $uri, $match))
            {
                continue;
            }
            else
            {
                $params['controller'] = $match['controller'];
                $params['action'] = $match['action'];
                $params['params'] = $match['params'];

                break;
            }
        }

        return $params;
    }
}