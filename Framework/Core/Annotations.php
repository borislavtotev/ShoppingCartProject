<?php
/**
 * Created by PhpStorm.
 * User: boris
 * Date: 10/2/2015
 * Time: 12:48 PM
 */

namespace SoftUni\Core;

class Annotations
{
    /**
     * Returns array with annotations for controllers in Areas part of the project
     * Each array for controller contains classAnnotations and methodAnnotations
     * Method annotations contains annotations for all methods in the class
     * All Route annotations are grouped under "Routes" in annotations. Route annotations can be set for the class and
     * on methods. If there is annotation only on the class, it is ignored.
    */
    public static function getAnnotations() {
        $filePaths = Annotations::getDirContents($_SERVER['DOCUMENT_ROOT']);
        $controllersFilePaths = Annotations::getControllersFilePaths($filePaths);

        $annotations = [];
        foreach ($controllersFilePaths as $controllersFilePath) {
            if (preg_match('/Application\\'.DIRECTORY_SEPARATOR.'Areas\\'.DIRECTORY_SEPARATOR.'(.*?)\\'.DIRECTORY_SEPARATOR.'Controllers\\'.DIRECTORY_SEPARATOR.'(.*?).php/',
                            $controllersFilePath, $match)) {

                $area = $match[1];
                $className = $match[2];
                var_dump($className);
                $fileName = $className.'.php';
                require_once '..'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'Areas'
                    .DIRECTORY_SEPARATOR.$area.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.$fileName;

                if (class_exists('SoftUni\\Application\\Areas\\'.$area.'\\Controllers\\'.$className)) {
                    $annotations[$className] = [];
                    $classRouteAnnotation = '';
                    $classAccessAnnotation = '';
                    $reflectionClass = new \ReflectionClass('SoftUni\\Application\\Areas\\'.$area.'\\Controllers\\'.$className);
                    $doc = $reflectionClass->getDocComment();
                    var_dump($doc);
                    if (preg_match_all('#@(.*?)\n#s', $doc, $newAnnotations)) {
                        foreach ($newAnnotations[1] as $newAnnotation) {
                            echo "new annotation";
                            var_dump($newAnnotation);
                            if (preg_match('/Route\((.*?)\)/', $newAnnotation, $matches)) {
                                //var_dump($matches);
                                $classRouteAnnotation = $matches[1];
                            }

                            $userRoles = UserRoles::getAllRoles();
                            $pattern = join("|", $userRoles);
                            var_dump($pattern);
                            echo "taaaaaaaa classs";
                            var_dump($newAnnotation);
                            if (preg_match('/'.$pattern.'/', $newAnnotation, $matches)) {
                                $classAccessAnnotation = $matches[0];
                            }
                        }
                    }
                    $methods = $reflectionClass->getMethods();
                    foreach ($methods as $method) {
                        $methodName = $method->getName();
                        $methodAccessAnnotation = '';
                        $methodDoc = $method->getDocComment();
                        if (preg_match_all('#@(.*?)\n#s', $methodDoc, $newMethodAnnotations)) {
                            foreach ($newMethodAnnotations[1] as $newMethodAnnotation) {
                                echo "method annotations";
                                var_dump($newMethodAnnotations[1]);

                                // Get Route Annotation
                                if (preg_match('/Route\((.*?)\)/', $newMethodAnnotation, $matches1)) {
                                    echo "matches";
                                    var_dump($matches1[1]);
                                    $fullRouteAnnotation = $classRouteAnnotation.'/'.$matches1[1];
                                    $fullRouteAnnotation = str_replace('"', '', $fullRouteAnnotation);
                                    $fullRouteAnnotation = str_replace("'", "", $fullRouteAnnotation);
                                    var_dump($fullRouteAnnotation);
                                    $annotations['Routes'][] = array($fullRouteAnnotation => [$className, $methodName]);
                                }

                                // Get Authorization Annotation
                                $userRoles = UserRoles::getAllRoles();
                                $pattern = join("|", $userRoles);
                                var_dump($pattern);
                                echo "taaaaaaaa";
                                var_dump($newMethodAnnotation);
                                if (preg_match('/'.$pattern.'/', $newMethodAnnotation, $matches)) {
                                    if (UserRoles::getRoleNumber($classAccessAnnotation) > $matches[0]) {
                                        $methodAccessAnnotation = $classAccessAnnotation;
                                    } else {
                                        $methodAccessAnnotation = $matches[0];
                                    }

                                    $annotations[$className][$methodName][] = array('Authorization' => $methodAccessAnnotation);
                                }

                                // Get HTTP Request Annotation
                                $pattern = "/GET|POST|PUT|DELETE/";
                                var_dump($pattern);
                                if (preg_match($pattern, $newMethodAnnotation, $matches2)) {
                                    $annotations[$className][$methodName][] = array('HttpRequest' => $matches2[0]);
                                }
                            }
                        }
                    }
                }
            }
            echo(json_encode($annotations, JSON_PRETTY_PRINT));
        }
    }

    public static function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if(is_dir($path) && $value != "." && $value != "..") {
                Annotations::getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    private function getControllersFilePaths($filePaths) {
        return array_filter($filePaths, function($filePath) {
            $pattern = '/Application\\' . DIRECTORY_SEPARATOR . 'Areas\\' . DIRECTORY_SEPARATOR
                . '(.*?)\\' . DIRECTORY_SEPARATOR. 'Controllers\\' . DIRECTORY_SEPARATOR . '(.*?).php/';
            if (preg_match($pattern, $filePath, $match)) {
                return $filePath;
            }
        });
    }
}
?>