<?php

session_start();

include_once('..' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR . 'Autoloader.php');
include_once('..' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Annotations.php');

use SoftUni\Core\Annotations;
Annotations::getAnnotations();

Autoloader::init();



//$uri = $_SERVER['REQUEST_URI'];
//$self = $_SERVER['PHP_SELF'];
//
//$directories = str_replace(basename($self), '', $self);
//var_dump($directories);
//$requestString = str_replace($directories, '', $uri);
//var_dump($requestString);
//
//$requestParams = explode("/", $requestString);
//
//$controller = array_shift($requestParams);
//$action = array_shift($requestParams);
//var_dump($controller);

//\SoftUni\Core\Database::setInstance(
//    \SoftUni\Config\DatabaseConfig::DB_INSTANCE,
//    \SoftUni\Config\DatabaseConfig::DB_DRIVER,
//    \SoftUni\Config\DatabaseConfig::DB_USER,
//    \SoftUni\Config\DatabaseConfig::DB_PASS,
//    \SoftUni\Config\DatabaseConfig::DB_NAME,
//    \SoftUni\Config\DatabaseConfig::DB_HOST
//);



//$app = new \SoftUni\Application();
//$app->start();
?>


