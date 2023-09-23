<?php

namespace ArtKoder\Peasy\Utils;

use ArtKoder\KvpParser\KvpParser;

class Files 
{
    public static function recursiveLoadTextRoutes($directory)
    {
        return KvpParser::parseRecursive($directory, ['routes']);
    }

    public static function recursiveLoadPhpRoutes($directory)
    {
        $files = KvpParser::recursiveScandir($directory);
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