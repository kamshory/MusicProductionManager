<?php

namespace MagicObject\Util\ClassUtil;

use MagicObject\SecretObject;
use MagicObject\Util\PicoStringUtil;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Object parser
 */
class PicoSecretParser
{
    /**
     * Parse SecretObject
     * @param SecretObject $data
     * @return SecretObject
     */
    private static function parseSecretObject($data)
    {
        $SecretObject = new SecretObject();
        $values = $data->value();
        foreach ($values as $key => $value) {
            $key2 = PicoStringUtil::camelize($key);
            if(is_scalar($value))
            {
                $SecretObject->set($key2, $value);
            }
            else
            {
                $SecretObject->set($key2, self::parseRecursiveObject($value));
            }
        }
        return $SecretObject;
    }
    
    /**
     * Parse Object
     * @param stdClass|array $data
     * @return SecretObject
     */
    private static function parseObject($data)
    {
        $SecretObject = new SecretObject();
        foreach ($data as $key => $value) {
            $key2 = PicoStringUtil::camelize($key);
            if(is_scalar($value))
            {
                $SecretObject->set($key2, $value);
            }
            else
            {
                $SecretObject->set($key2, self::parseRecursiveObject($value));
            }
        }
        return $SecretObject;
    }
    
    /**
     * Check if input is associated array
     *
     * @param array $array
     * @return boolean
     */
    private static function hasStringKeys($array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
    
    /**
     * Parse recursive
     * @param mixed $data
     * @return mixed
     */
    public static function parseRecursiveObject($data)
    {
        $result = null;
        if($data != null)
        {
            if($data instanceof SecretObject)
            {
                $result = self::parseSecretObject($data);
            }
            else if (is_array($data) || is_object($data) || $data instanceof stdClass) {
                $obj = new SecretObject();
                foreach($data as $key=>$val)
                {
                    $obj = self::updateObject($obj, $key, $val);
                }
                $result = $obj;
            }
            else
            {
                $result = $data;
            }
        }
        return $result;
    }
    
    /**
     * Update object
     *
     * @param SecretObject $obj
     * @param string $key
     * @param mixed $val
     * @return SecretObject
     */
    private static function updateObject($obj, $key, $val)
    {
        if (self::isObject($val))
        {
            $obj->set($key, self::parseRecursiveObject($val));
        }
        else if (is_array($val))
        {
            if(self::hasStringKeys($val))
            {
                $obj->set($key, self::parseRecursiveObject($val));
            }
            else
            {
                $obj->set($key, self::parseRecursiveArray($val));
            }
        }
        else
        {
            $obj->set($key, $val);
        }
        return $obj;
    }
    
    /**
     * Check if value is object
     *
     * @param [type] $value
     * @return boolean
     */
    private static function isObject($value)
    {
        if ($value instanceof stdClass || is_object($value))  
        {
            return true;
        }
        return false;
    }
    
    /**
     * Parse recursive
     * @param array $data
     */
    public static function parseRecursiveArray($data)
    {
        $result = array();
        if($data != null)
        {
            foreach($data as $val)
            {
                if (self::isObject($val))
                {
                    $result[] = self::parseRecursiveObject($val);
                }
                else if (is_array($val))
                {
                    if(self::hasStringKeys($val))
                    {
                        $result[] = self::parseRecursiveObject($val);
                    }
                    else
                    {
                        $result[] = self::parseRecursiveArray($val);
                    }
                }
                else
                {
                    $result[] = $val;
                }
            }
        }
        return $result;
    }
    
    /**
     * Parse from Yaml recursively
     */
    public static function parseYamlRecursive($yamlString)
    {
        if($yamlString != null)
        {
            $data = Yaml::parse($yamlString);
            if (is_array($data) || is_object($data)) {
                return self::parseObject($data);
            }
        }
        return null;
    }
    
    /**
     * Parse from JSON recursively
     */
    public static function parseJsonRecursive($jsonString)
    {
        if($jsonString != null)
        {
            $data = json_decode($jsonString);
            if (is_array($data) || is_object($data)) {
                return self::parseObject($data);
            }
        }
        return null;
    }
}