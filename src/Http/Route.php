<?php

namespace ArtKoder\Peasy\Http;

use ReflectionMethod;

class Route 
{
    private string $name;
    
    private string $path;

    private string $method;

    private string $regex;

    private array $controller;

    public function __construct(string $path, string $method, string $controller, string $name = '')
    {
        $this->path = $path;
        $this->method = $method;
        $this->regex = self::pathToRegex($path);
        $this->controller = explode('::', $controller);
        $this->name = $name;
    }

    
    private static function pathToRegex(string $path)
    {
        // Special regex for home page
        if ($path="/") {
            return "#^/$#";
        }
        
        $replacePattern = "#{(.+?)}#";
        return "#^" . preg_replace($replacePattern, '(?<$1>.+)', $path) . "/?$#";
    }

    public static function new(array $attributes)
    {
        $name = $attributes['name'] ?? '';
        return new self(
            $attributes['path'], 
            $attributes['method'],
            $attributes['controller'],
            $name
        );
    }

    public function match($path)
    {
        $success = preg_match($this->regex, $path, $matches);
        if ($success) {
            return $matches;
        } 
        return false;
    }

    public function callController($arguments, string $queryString = '') {
        if (!empty($queryString)) {
            parse_str($queryString, $queryParams);
            $arguments = array_merge($arguments, $queryParams);
        }
        if (method_exists($this->getControllerClass(), $this->getControllerMethod())) {
            $reflectionMethod = new ReflectionMethod($this->getControllerClass(), $this->getControllerMethod());
            $numberOfParameters = $reflectionMethod->getNumberOfParameters();

            if (count($arguments) !== $numberOfParameters) {
                error_log ("Mismatch in the number of parametters in " . __FILE__ . ' line ' . __LINE__);
                return false;
            } 

            return call_user_func_array($this->controller, $arguments);                           
        } else {
            error_log("Method {$this->getControllerClass()}::{$this->getControllerMethod()} does not exist" 
                . __FILE__ . ' line ' . __LINE__);
        }
        return false;
    }

    public function getControllerClass()
    {
        return $this->controller[0];
    }

    public function getControllerMethod()
    {
        return $this->controller[1];
    }
}