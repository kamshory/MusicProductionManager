<?php

namespace MagicObject\Util\Database;

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
     * @param string $value Value
     * @param string $type Data type
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
    
    /**
	 * Escape value
	 * @var mixed
	 * @return string
	 */
	public static function escapeValue($value)
	{
		if($value === null)
		{
			// null
			$ret = 'null';
		}
		else if(is_string($value))
		{
			// escape the value
			$ret = "'".self::escapeSQL($value)."'";
		}
		else if(is_bool($value))
		{
			// true or false
			$ret = $value?'true':'false';
		}
		else if(is_numeric($value))
		{
			// convert number to string
			$ret = $value."";
		}
		else if(is_array($value) || is_object($value))
		{
			// encode to JSON and escapethe value
			$ret = "'".self::escapeSQL(json_encode($value))."'";
		}
		else
		{
			// force convert to string and escapethe value
			$ret = "'".self::escapeSQL($value)."'";
		}
		
		return $ret;
	}
    
    /**
     * Escape SQL
     *
     * @param string $value
     * @return string
     */
    public static function escapeSQL($value)
    {
        return addslashes($value);
    }
    
    /**
     * Trim WHERE
     *
     * @param string $where
     * @return string
     */
    public static function trimWhere($where)
    {
        // DO NOT EDIT THIS CONSTANT
        if(stripos($where, "(1=1) or ") === 0)
        {
            $where = substr($where, 9);
        }
        // DO NOT EDIT THIS CONSTANT
        if(stripos($where, "(1=1) and ") === 0)
        {
            $where = substr($where, 10);
        }
        return $where;
    }

    /**
     * Generate UUID
     *
     * @return string
     */
    public static function uuid()
    {
        $uuid = uniqid();
		if ((strlen($uuid) % 2) == 1) {
			$uuid = '0' . $uuid;
		}
		$random = sprintf('%06x', mt_rand(0, 16777215));
		return sprintf('%s%s', $uuid, $random);
    }
}