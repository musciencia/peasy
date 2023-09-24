<?php

namespace ArtKoder\Peasy\Http;

use ReflectionMethod;

class Route 
{
    const CLASS_INDEX = 0;
    const METHOD_INDEX = 1;
    
    private string $name;
    
    private string $path;

    private string $method;

    private string $regex;

    private array $controller;

    /**
     * Creates an instance of the Route class
     * 
     * @param string $path        The path pattern for this route
     * @param string $method      The request method, e.g. GET, POST
     * @param string $controller  The controller that will handle the request e.g. Namespace\Class::method
     * @param string $name        The name of this route (optional)
     */
    public function __construct(string $path, string $method, string $controller, string $name = '')
    {
        $this->path = $path;
        $this->method = $method;
        $this->regex = self::pathToRegex($path);        
        $this->controller = explode('::', $controller);
        $this->name = $name;
    }

    /**
     * Creates a regular expression from the $path pattern. This regex will be used to check if a path
     * can be handled by this route using the 'match' method
     * 
     * @param string $path
     * @return string
     */
    public static function pathToRegex(string $path)
    {
        // Special regex for home page
        if ($path === "/") {
            return "#^/$#";
        }
        
        // Use to replace the variable placeholders like {some-var}
        $replacePattern = "#{(.+?)}#";
        
        return "#" . preg_replace($replacePattern, '(?<$1>[^\/]+)', $path) . "#";
    }

    /**
     * Creates a new instance of Route from an array of attributes.
     * Same as the constructor but more flexible.
     * 
     * @param array $attributes
     * @return \self
     */
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

    /**
     * Checks if the $path matches this route. In other words
     * it checks if this route can handle the path.
     * 
     * @param type $path
     * @return bool
     */
    public function match($path)
    {
        $success = preg_match($this->regex, $path, $matches);
        if ($success) {
            return $matches;
        } 
        return false;
    }

    // TODO: only queries can be optional unless we add the functionality
    // to the route like /path/{mandatory}/{?optional} but it may
    // get messy. You can always create a separate controller in those cases.
    public function callController($arguments, string $queryString = '') {
        if (!empty($queryString)) {
            parse_str($queryString, $queryParams);
            $arguments = array_merge($arguments, $queryParams);
        }

        if (method_exists($this->getControllerClass(), $this->getControllerMethod())) {
            if (!$this->argumentsMatch($arguments)) {
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
    
    /**
     * Verifies if the number of $arguments coming from the URL including query parameters
     * matches the number of parameters that the controller's method expects
     * 
     * @param type $arguments The arguments coming from the URL
     * @return bool           True if the arguments match the controller's signature, false otherwise
     */
    public function argumentsMatch($arguments): bool
    {
        // Get a ReflectionMethod instance for your method
        $reflection = new ReflectionMethod($this->getControllerClass(), $this->getControllerMethod());

        // Get the parameters of the method
        $parameters = $reflection->getParameters();

        // Count the number of required parameters
        $requiredParameters = 0;
        foreach ($parameters as $parameter) {
            if (!$parameter->isOptional()) {
                $requiredParameters++;
            }
        }

        // Get last parameter
        $lastParam = end($parameters);
        
        $maximumParametersAllowed = 0;
        if ($lastParam !== false) {
            $maximumParametersAllowed = $lastParam->isVariadic() ? PHP_INT_MAX : count($parameters);        
        }

        $numberOfArguments = count($arguments);
                
        return $numberOfArguments >= $requiredParameters && $numberOfArguments <= $maximumParametersAllowed;                
    }

    /**
     * Returns the name of the controller's class
     * 
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controller[self::CLASS_INDEX];
    }

    /**
     * Returns the name of the controller's method
     * 
     * @return string
     */
    public function getControllerMethod()
    {
        return $this->controller[self::METHOD_INDEX];
    }
}