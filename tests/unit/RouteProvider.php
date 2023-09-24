<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Description of RouteProvider
 *
 * @author francisco
 */
class RouteProvider extends TestCase {
    
    public static function argumentsMatch()
    {
        return [
            'home without queries' => [
                'arguments' => [],
                'path' => '/',
                'controller' =>'Tests\Unit\TestControllers\TestController::home',
                'expectedMatch' => true
            ],
            'one argument' => [
                'arguments' => ['arg1'],
                'path' => '/path/{arg1}',
                'controller' =>'Tests\Unit\TestControllers\TestController::oneArgument',
                'expectedMatch' => true
            ],
            'two arguments plus variable length' => [
                'arguments' => ['arg1', 'arg2', 'arg3', 'arg4', 'arg5'],
                'path' => '/path/{arg1}/{arg2}',
                'controller' =>'Tests\Unit\TestControllers\TestController::twoArgumentsPlusVariableLength',
                'expectedMatch' => true
            ],
            'two arguments plus variable length, no queries' => [
                'arguments' => ['arg1', 'arg2'],
                'path' => '/path/{arg1}/{arg2}',
                'controller' =>'Tests\Unit\TestControllers\TestController::twoArgumentsPlusVariableLength',
                'expectedMatch' => true
            ],
            'two arguments in pattern, one passed to controller' => [
                'arguments' => ['arg1'],
                'path' => '/path/{arg1}/{arg2}',
                'controller' =>'Tests\Unit\TestControllers\TestController::twoArguments',
                'expectedMatch' => false
            ],
            // TODO add test cases whith optional query params present
        ];
    }
}
