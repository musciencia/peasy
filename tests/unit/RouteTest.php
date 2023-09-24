<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use ArtKoder\Peasy\Http\Route;
/**
 * Description of RouteTest
 *
 * @author francisco
 */
class RouteTest extends TestCase {
    
    /**
     * 
     * @param array $arguments     The arguments that the Router will pass to the controller
     * @param string $path         The path pattern
     * @param string $controller   The controller's class
     * @param bool $expectedMatch  Whether the number of arguments match the controller's method signature
     * @dataProvider \Tests\Unit\RouteProvider::argumentsMatch
     */
    public function testArgumentsMatch(
            array $arguments,
            string $path,
            string $controller,
            bool $expectedMatch)
    {
        $route = new Route($path, 'GET', $controller);
        $actualMatch = $route->argumentsMatch($arguments);
        $this->assertEquals($expectedMatch, $actualMatch);
    }
}
