<?php
namespace MagicObject\Util;

class StringUtil
{
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
    
    /**
     * Check if string is starts with substring
     *
     * @param string $haystack
     * @param string $value
     * @param boolean $caseSensitive
     * @return boolean
     */
    public static function startsWith($haystack, $value, $caseSensitive = false)
    {
        if($caseSensitive)
        {
            return isset($haystack) && substr($haystack, 0, strlen($value)) == $value;
        }
        else
        {
            return isset($haystack) && strtolower(substr($haystack, 0, strlen($value))) == strtolower($value);
        }
    }
    
    /**
     * Check if string is ends with substring
     *
     * @param string $haystack
     * @param string $value
     * @param boolean $caseSensitive
     * @return boolean
     */
    public static function endsWith($haystack, $value, $caseSensitive = false)
    {
        if($caseSensitive)
        {
            return isset($haystack) && substr($haystack, strlen($haystack) - strlen($value)) == $value;
        }
        else
        {
            return isset($haystack) && strtolower(substr($haystack, strlen($haystack) - strlen($value))) == strtolower($value);
        }
    }
    
    /**
     * Check if string is not null and not empty
     *
     * @param string $value
     * @return boolean
     */
    public static function isNotNullAndNotEmpty($value)
    {
        return isset($value) && !empty($value);
    }
    
    /**
     * Check if string is null or empty
     *
     * @param string $value
     * @return boolean
     */
    public static function isNullOrEmpty($value)
    {
        return !isset($value) || empty($value);
    }
    
    /**
     * Select not null value
     *
     * @param mixed $value1
     * @param mixed $value2
     * @return mixed
     */
    public static function selectNotNull($value1, $value2)
    {
        return isset($value1) ? $value1 : $value2;
    }
    
}