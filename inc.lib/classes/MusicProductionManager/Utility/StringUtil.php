<?php

namespace MusicProductionManager\Utility;

class StringUtil
{
    /**
     * Fixing cariage return
     *
     * @param string $text
     * @return string
     */
    public static function fixingCariageReturn($text)
    {
        $text = str_replace("\n", "\r\n", $text);
        $text = str_replace("\r\r\n", "\r\n", $text);
        $text = str_replace("\r", "\r\n", $text);
        $text = str_replace("\r\n\n", "\r\n", $text);
        return $text;
    }
    
    /**
     * Return attribute selected on select > option
     *
     * @param bool $condition
     * @return string
     */
    public static function formSelected($condition)
    {
        return $condition ? ' selected':'';
    }
    
    /**
     * Return attribute checked on input[type=checkbox]
     *
     * @param bool $condition
     * @return string
     */
    public static function formChecked($condition)
    {
        return $condition ? ' checked':'';
    }
    
    /**
     * Convert snake case to camel case
     *
     * @param string $input
     * @param string $glue
     * @return string
     */
    public static function camelize($input, $glue = '_')
    {
        return lcfirst(str_replace($glue, '', ucwords($input, $glue)));
    }
    
    /**
     * Convert snake case to upper camel case
     *
     * @param string $input
     * @param string $glue
     * @return string
     */
    public static function upperCamelize($input, $glue = '_')
    {
        return ucfirst(str_replace($glue, '', ucwords($input, $glue)));
    }

    /**
     * Convert camel case to snake case
     *
     * @param string $input
     * @param string $glue
     * @return string
     */
    public static function snakeize($input, $glue = '_') {
        return ltrim(
            preg_replace_callback('/[A-Z]/', function ($matches) use ($glue) {
                return $glue . strtolower($matches[0]);
            }, $input),
            $glue
        );
    }
}