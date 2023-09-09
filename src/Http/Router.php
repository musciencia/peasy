<?php

namespace ArtKoder\Peasy\Http;

use ArtKoder\Peasy\Utils\Files;
use ArtKoder\Peasy\Http\Route;

class Router
{
    private $routeDirectory;

    /**
     * @var Route[]
     */
    private $routes = [];

    public function __construct($routesDirectory)
    {
        $loadedRoutes = Files::loadRoutes($routesDirectory);
        foreach ($loadedRoutes as $loadedRoute) {
            if (array_key_exists('name', $loadedRoute)) {
                $this->routes[$loadedRoute['name']] = Route::new($loadedRoute);
            } else {
                $this->routes[] = Route::new($loadedRoute);
            }
        }
    }

    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parsedUri = parse_url($uri);
        $path = $parsedUri['path'] ?? '';
        $query = $parsedUri['query'] ?? '';
        // print_r($parsedUri);
        foreach ($this->routes as $name => $route) {
            $matches = $route->match($path);
            if (!empty($matches)) {
                $arguments = array_filter($matches, function($key){
                    return !is_int($key);
                }, ARRAY_FILTER_USE_KEY);
                
                return $route->callController($arguments, $query);
            }
        }
    }

    public static function redirect($path)
    {
        header("Location: $path");
        exit();
    }
}
