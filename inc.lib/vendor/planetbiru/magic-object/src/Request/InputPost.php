<?php

namespace MagicObject\Request;

use MagicObject\Util\ClassUtil\PicoObjectParser;

/**
 * Input Post
 * @link https://github.com/Planetbiru/MagicObject
 */
class  InputPost extends PicoRequestBase {
    /**
     * Recursive
     *
     * @var boolean
     */
    private $_recursive = false; //NOSONAR

    /**
     * Constructor
     * @param boolean $recursive Flag to convert all objects recusrsively
     * @param boolean $parseNullAndBool Parse NULL and BOOL
     * @param boolean $forceScalar Get scalar value only
     */
    public function __construct($recursive = false, $parseNullAndBool = false, $forceScalar = false)
    {
        parent::__construct($forceScalar);
        $this->_recursive = $recursive; 
        if($parseNullAndBool)
        {
            $this->loadData($this->forceBoolAndNull($_POST));
        }
        else
        {
            $this->loadData($_POST);
        }
    }

    /**
     * Get global variable $_POST
     *
     * @return array
     */
    public static function requestPost()
    {
        return $_POST;
    }

    /**
     * Override loadData
     *
     * @param array $data Data to load
     * @return self
     */
    public function loadData($data, $tolower = false)
    {
        if($this->_recursive)
        {
            $genericObject = PicoObjectParser::parseJsonRecursive($data);
            if($genericObject != null)
            {
                $values = $genericObject->valueArray();
                if($values != null && is_array($values))
                {
                    $keys = array_keys($values);
                    foreach($keys as $key)
                    {
                        $this->{$key} = $genericObject->get($key);
                    }
                }
            }
        }
        else
        {
            parent::loadData($data);
        }
        return $this;
    } 
}