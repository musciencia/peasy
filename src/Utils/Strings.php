<?php

namespace ArtKoder\Peasy\Utils;

/**
 * Utilities to manipulate strings
 */
class Strings
{

    /**
     * Converts a string to PascalCase
     * Spaces " ", dashes, "-" and underscores "_"should be removed
     *   e.g. "hello my-good_friend" will become "HelloMyGoodFriend"
     */
    public static function toPascalCase($string)
    {
        $splittedString = preg_split('/[-_\s]+/', strtolower($string));
        $pascalCaseString = '';
        foreach ($splittedString as $element) {
            $pascalCaseString .= ucfirst($element);
        }
        return $pascalCaseString;
    }

    /**
     * Converts a string to camelCase
     *   e.g. "hello my-good_friend" will become "helloMyGoodFriend"
     */
    public static function toCamelCase($string)
    {
        // Use the function toPascalCase() and then just make the first letter lowercase
        $pascalCase = self::toPascalCase($string);
        $camelCase = lcfirst($pascalCase);
        return $camelCase;
    }

    public static function camelToSnakeCase($string) {
        $string = preg_replace('/\s+/', '_' , $string);
        $splittedString = preg_split('/(?=[A-Z])/', lcfirst($string));
        $splittedString = array_map(function($word){
            return strtolower($word);
        }, $splittedString);
        return implode("_", $splittedString);
    }

    public static function divToNewLine($string)
    {
        $string = str_replace("<div>", "\n", $string);
        return str_replace("</div>", "", $string);
    }
}
