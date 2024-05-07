<?php

namespace MagicObject\Request;

use MagicObject\Util\ClassUtil\PicoObjectParser;

class  InputEnv extends PicoRequestBase {
    /**
     * Recursive
     *
     * @var boolean
     */
    private $_recursive = false; //NOSONAR

    /**
     * Constructor
     * @param boolean $recursive
     * @param boolean $parseNullAndBool
     */
    public function __construct($recursive = false, $parseNullAndBool = false)
    {
        parent::__construct();
        $this->_recursive = $recursive; 
        if($parseNullAndBool)
        {
            $this->loadData($this->forceBoolAndNull($_ENV));
        }
        else
        {
            $this->loadData($_ENV);
        }
    }

    /**
     * Override loadData
     *
     * @param array $data
     * @return self
     */
    public function loadData($data)
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