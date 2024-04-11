<?php

namespace MagicObject\Util;

use MagicObject\MagicObject;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Object parser
 */
class PicoObjectParser
{
    /**
     * Parse MagicObject
     * @param MagicObject $data
     * @return MagicObject
     */
    private static function parseMagicObject($data)
    {
        $magicObject = new MagicObject();
        $values = $data->value();
        foreach ($values as $key => $value) {
            $key2 = PicoStringUtil::camelize($key);
            if(is_scalar($value))
            {
                $magicObject->set($key2, $value, true);
            }
            else
            {
                $magicObject->set($key2, self::parseRecursive($value), true);
            }
        }
        return $magicObject;
    }
    
    /**
     * Parse Object
     * @param stdClass|array $data
     * @return MagicObject
     */
    private static function parseObject($data)
    {
        $magicObject = new MagicObject();
        foreach ($data as $key => $value) {
            $key2 = PicoStringUtil::camelize($key);
            if(is_scalar($value))
            {
                $magicObject->set($key2, $value, true);
            }
            else
            {
                $magicObject->set($key2, self::parseRecursive($value), true);
            }
        }
        return $magicObject;
    }
    
    /**
     * Parse recursive
     */
    public static function parseRecursive($data)
    {
        if($data != null)
        {
            if($data instanceof MagicObject)
            {
                return self::parseMagicObject($data);
            }
            else if (is_array($data) || is_object($data)) {
                return self::parseObject($data);
            }
        }
        return null;
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