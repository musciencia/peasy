<?php

namespace ArtKoder\Peasy\Utils;

use Symfony\Component\Yaml\Yaml;

class Files 
{
    public static function recursiveScandir($directory)
    {
        $result = [];

        $files = scandir($directory);

        foreach ($files as $file)
        {
            if (in_array($file,array(".",".."))) {
                continue;
            }

            $filePath = realpath($directory . DIRECTORY_SEPARATOR . $file); 
            if (is_dir($filePath))
            {
                $dirsToAppend = self::recursiveScandir($filePath);
                $result = array_merge($result, $dirsToAppend);
            } else {
                $result[] = $filePath;
            } 

        }
        return $result;
    }

    public static function recursiveLoadYaml($directory)
    {
        $files = self::recursiveScandir($directory);
        $yamlFiles =  array_filter($files, function($file){
            $pathParts = pathinfo($file);
            return in_array($pathParts['extension'], ['yaml','yml']);
        });


        $result = [];
        foreach ($yamlFiles as $yamlFile) {
            $parsedYaml = Yaml::parseFile($yamlFile);
            $result = array_merge($result,$parsedYaml);
        }

        return $result;
    }

    public static function parseRoutesFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $filePointer = fopen($filePath, 'r');

        if ($filePointer === false) {
            return false;
        }

        $data = []; 
        $item = [];
        
        while (($line = fgets($filePointer)) !== false) {
            $line = trim($line);

            if (empty($line)) {
                if (!empty($item)) {
                    $data[] = $item;
                    $item = [];
                }
            } else {
                list($key, $value) = explode(": ", $line, 2);
                $item[$key] = $value;
            }
        }
            
        if (!empty($item)) {
            $data[] = $item;
        }

        fclose($filePointer);

        return $data;        
    }

    public static function recursiveLoadTextRoutes($directory)
    {
        $files = self::recursiveScandir($directory);
        $routesFiles =  array_filter($files, function($file){
            $pathParts = pathinfo($file);
            return $pathParts['extension'] === 'routes';
        });


        $result = [];
        foreach ($routesFiles as $routesFile) {
            $parsedRoutes = self::parseRoutesFile($routesFile);
            $result = array_merge($result,$parsedRoutes);
        }

        return $result;
    }

    public static function recursiveLoadPhpRoutes($directory)
    {
        $files = self::recursiveScandir($directory);
        $phpRouteFiles =  array_filter($files, function($file){
            $pathParts = pathinfo($file);
            return $pathParts['extension'] === 'php';
        });

        $result = [];
        foreach ($phpRouteFiles as $phpRoutesFile) {
            $includedRoutes = include $phpRoutesFile;
            $result = array_merge($result, $includedRoutes);
        }

        return $result;
    }

    public static function loadRoutes($directory)
    {
        $textRoutes = self::recursiveLoadTextRoutes($directory);
        $phpRoutes = self::recursiveLoadPhpRoutes($directory);
        return array_merge($textRoutes, $phpRoutes);
    }
}