<?php

namespace Gionin;

/**
 * Class to traintament encoding UTF8
 * @package default
 * @author  Raphael Giovanini
**/
class Utf8String
{
    /**
     * Test if a utf8
     * @param string $text  Any string.
     * @return string  The same string, UTF8 encoded
     **/
    public static function isUTF8($text)
    {
        return utf8_encode(utf8_decode($text)) == $text;
    }

    /**
     * Recursive encode
     * @param string $text  Any string.
     * @return string  The same string, UTF8 encoded
     **/
    public static function recursiveDecode($text)
    {
        while (self::isUTF8($text)) {
            if (utf8_decode($text) == $text) {
                break;
            }
            $text = utf8_decode($text);
        }
        return $text;
    }

    /**
     * Rebase encode
     * @param string $text  Any string.
     * @return string  The same string, UTF8 encoded
     **/
    public static function rebaseEncode($text)
    {
        return utf8_encode(self::recursiveDecode($text));
    }

    /**
     * Remove accentuation
     * @param string $text  Any string.
     * @return string  The same string, UTF8 encoded
     **/
    public static function noAccents($text)
    {
        return preg_replace(
            [
                '/&szlig;/',
                '/&(..)lig;/',
                '/&([aouAOU])uml;/',
                '/&(.)[^;]*;/'
            ],
            [
                'ss',
                "$1",
                "$1",
                "$1"
            ],
            htmlentities(self::rebaseEncode($text), ENT_COMPAT, 'UTF-8')
        );
    }

    /**
     * Make a string lowercase and remove accentuation
     * @param string $text  Any string.
     * @return string  The same string, UTF8 encoded
     **/
    public static function lowerAndNoAccents($text = '')
    {
        return strtolower(self::noAccents($text));
    }
}
