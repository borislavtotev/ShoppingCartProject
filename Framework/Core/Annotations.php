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
                    $reflectionClass = new \ReflectionClass('SoftUni\\Application\\Areas\\'.$area.'\\Controllers\\'.$className);
                    $doc = $reflectionClass->getDocComment();
                    var_dump($doc);
                    preg_match_all('#@(.*?)\n#s', $doc, $newAnnotations);
                    $annotations[$className]['classAnnotations'] = $newAnnotations;
                    var_dump($newAnnotations);
                    $methods = $reflectionClass->getMethods();
                    foreach ($methods as $method) {
                        $methodDoc = $method->getDocComment();
                        preg_match_all('#@(.*?)\n#s', $methodDoc, $newMethodAnnotations);
                        $annotations[$className]['methodAnnotations'][$method->getName()] = $newMethodAnnotations;
                    }
                }
            }
            print_r($annotations);
        }
    }

    private function getDirContents($dir, &$results = array()){
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