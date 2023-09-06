<?php

namespace ArtKoder\Peasy\Http;

use ArtKoder\Peasy\Utils\Strings;
use ReflectionMethod;
use ReflectionException;
use ArgumentCountError;

/**
 * This class is reponsible for processing the http request by
 * calling the right constructor.
 * 
 * Example: 
 *  
 * With this Request
 * GET http://domain.com/controller-class/function-to-call/arg1/arg2/arg3?key1=value1&key2=value2
 * 
 * The router will call the following function
 * ArtKoder\Peasy\Controllers\ControllerClass::getFunctionToCall(ar1, arg2, arg3, ['key1'=>'value1','key2'=>'value2']);
 * 
 * Usage:
 *
 *      Router::handleRequest();
 * 
 *  In order for the Router to work, you need to create a controller to handle the request, for example:
 * 
 *    For the following request: POST http://localhost:8000/users/delete/2
 *     
 *    You need to create a ArtKoder\Peasy\Controllers\Users with a method delete() that handles the request
 *    
 *    For detailed information about controllers, read section "Controllers" in README.md  
 *   
 */
class Router
{
    private string $controllersNamespace;

    /**
     * @var Router The only instance of Router
     */
    private static $instance;

    /**
     * @var string The name of the conroller's class
     */
    private $controllerClass;

    /**
     * @var string The name of controller's function 
     */
    private $controllerFunction;

    /**
     * @var array The arguments to pass to the controller's function
     */
    private $functionArguments;

    /**
     * @var array A list of query parameters
     */
    private $queryParameters;

    private $path;

    /**
     * Creates an instance of router using data from $_SERVER
     * The constructor pupulates the private variables following the route rules outlined 
     * in README.md
     */
    public function __construct(string $conrollersNamespace)
    {
        $this->controllersNamespace = $conrollersNamespace;
        $url = $_SERVER['REQUEST_URI'];
        $splittedUrl = explode('?', $url);
        $this->path = trim($splittedUrl[0], '/');
        $splittedPath = explode('/', $this->path);

        $this->controllerClass = array_shift($splittedPath);
        $this->controllerFunction = array_shift($splittedPath);
        $this->functionArguments = $splittedPath;
        $this->queryParameters = '';
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'GET') {
            $this->queryParameters = $_GET;
        }

        if ($requestMethod === 'POST') {
            $this->queryParameters = $_POST;
        }

        $this->controllerClass = empty($this->controllerClass) ? 'Home' : Strings::toPascalCase($this->controllerClass);
        $this->controllerClass = "{$this->controllersNamespace}$this->controllerClass";

        $this->controllerFunction = empty($this->controllerFunction) ? 'Index' : Strings::toPascalCase($this->controllerFunction);
        $this->controllerFunction = strtolower($requestMethod) . $this->controllerFunction;
    }


    /**
     * @return string The controller's class name
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @return string The controller's function name
     */
    public function getControllerFunction()
    {
        return $this->controllerFunction;
    }

    /**
     * @return array The arguments to pass to the controller's function
     */
    public function getFunctionArguments()
    {
        return $this->functionArguments;
    }

    /**
     * @return array A list of query parameters
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }


    public function getPath()
    {
        return $this->path;
    }

    // /** 
    //  * If the Router instance exists, returns it. If it does not exist, creates a new instance and
    //  * then returns it.
    //  *  
    //  * @return Router The only instance of Router
    //  */
    // public static function getInstance()
    // {
    //     if (empty(self::$instance)) {
    //         self::$instance = new Router();
    //     }
    //     return self::$instance;
    // }

    // public static function handleRequest()
    // {
    //     return self::getInstance()->processRequest();
    // }

    /**
     * Calls the controller's function
     * @return mixed Whatever the controller's function returns
     */
    public function handleRequest()
    {
        try {
            $method = new ReflectionMethod($this->controllerClass, $this->controllerFunction);
            
            $args = $this->functionArguments;
            if (!empty($this->queryParameters)) {
                $args[] = $this->queryParameters; 
            }

            return $method->invokeArgs(null, $args);
        } catch (ReflectionException $reflectionError) {
            http_response_code(404);
            echo "Page not found: 404";
        } catch (ArgumentCountError $argumentError) {
            http_response_code(404);
            echo "Page not found: 404";
        }     
    }

    public static function redirect($path)
    {
        header("Location: $path");
        exit();
    }
}
