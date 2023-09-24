<?php


namespace Tests\Unit\TestControllers;

/**
 * Description of TestController
 *
 * @author francisco
 */
class TestController {
    public static function home()
    {
        return [];
    }
    
    public static function oneArgument($arg1) {
        return [$arg1];
    }

    public static function twoArgumentsPlusVariableLength($arg1, $arg2, ...$queries) {
        return [$arg1, $arg2, $queries];
    }    

    public static function twoArguments($arg1, $arg2) {
        return [$arg1, $arg2];
    }        

    public static function twoArgumentsSecondOptional($arg1, $arg2="optional") {
        return [$arg1, $arg2];
    }        
}
