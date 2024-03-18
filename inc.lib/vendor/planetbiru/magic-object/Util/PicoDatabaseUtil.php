<?php

namespace MagicObject\Util;

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;

class PicoDatabaseUtil
{
    /**
     * Get specification from parameters
     * @param array $params
     * @return PicoSpecification|null
     */
    public static function specificationFromParams($params)
    {
        if(isset($params) && is_array($params))
        {
            foreach($params as $param)
            {
                if($param instanceof PicoSpecification)
                {
                    return $param;
                }
            }
        }
        return null;
    }

    /**
     * Get pagable from parameters
     * @param array $params
     * @return PicoPagable|null
     */
    public static function pagableFromParams($params)
    {
        if(isset($params) && is_array($params))
        {
            foreach($params as $param)
            {
                if($param instanceof PicoPagable)
                {
                    return $param;
                }
            }
        }
        return null;
    }
    
    /**
     * Get sortable from parameters
     * @param array $params
     * @return PicoSortable|null
     */
    public static function sortableFromParams($params)
    {
        if(isset($params) && is_array($params))
        {
            foreach($params as $param)
            {
                if($param instanceof PicoSortable)
                {
                    return $param;
                }
            }
        }
        return null;
    }

    /**
     * Get pagable from parameters
     * @param array $params
     * @return array
     */
    public static function valuesFromParams($params)
    {
        $ret = array();
        if(isset($params) && is_array($params))
        {
            foreach($params as $param)
            {
                if($param instanceof PicoPagable)
                {
                    break;
                }
                $ret[] = $param;
            }
        }
        return $ret;
    }
    
    /**
     * Fix value
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    public static function fixValue($value, $type) // NOSONAR
    {
        if(strtolower($value) === 'true')
        {
            return true;
        }
        else if(strtolower($value) === 'false')
        {
            return false;
        }
        else if(strtolower($value) === 'null')
        {
            return false;
        }
        else if(is_numeric($value) && strtolower($type) != 'string')
        {
            return $value + 0;
        }
        else 
        {
            return $value;
        }
    }
}