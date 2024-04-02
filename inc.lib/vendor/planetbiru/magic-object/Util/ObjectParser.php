<?php

namespace MagicObject\Util;

use MagicObject\MagicObject;
use stdClass;

class ObjectParser
{
    /**
     * Parse Magic Object
     * @param MagicObject $data
     * @return MagicObject
     */
    private function parseMagicObject($data)
    {
        $magicObject = new MagicObject();
        $values = $data->value();
        foreach ($values as $key => $value) {
            $key2 = StringUtil::camelize($key);
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
    private function parseObject($data)
    {
        $magicObject = new MagicObject();
        foreach ($data as $key => $value) {
            $key2 = StringUtil::camelize($key);
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
}