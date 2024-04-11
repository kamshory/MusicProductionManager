<?php

namespace MagicObject;

use MagicObject\Util\PicoStringUtil;

class PicoLanguage
{
    /**
     * Constructor
     *
     * @param stdClass|array $data
     */
    public function __construct($data = null)
    {
        if(isset($data))
        {
            $this->loadData($data);
        }
    }
    
    /**
     * Load data to object
     * @param stdClass|array $data
     * @return self
     */
    public function loadData($data)
    {
        if($data != null && (is_array($data) || is_object($data)))
        {
            foreach ($data as $key => $value) {
                $key2 = PicoStringUtil::camelize($key);
                $this->set($key2, $value);
            }
        }
        return $this;
    }

    /**
     * Set property value
     *
     * @param string $propertyName
     * @param mixed|null
     * @return self
     */
    public function set($propertyName, $propertyValue)
    {
        $var = PicoStringUtil::camelize($propertyName);
        $this->$var = $propertyValue;
        return $this;
    }

    /**
     * Get property value
     *
     * @param string $propertyName
     * @return mixed|null
     */
    public function get($propertyName)
    {
        $var = PicoStringUtil::camelize($propertyName);
        return isset($this->$var) ? $this->$var : null;
    }
    
    /**
     * Check if property value is set
     *
     * @param string $propertyName
     * @return boolean
     */
    public function isset($propertyName)
    {
        $var = PicoStringUtil::camelize($propertyName);
        return isset($this->$var);
    }
    
    /**
     * Stores datas in the property.
     * Example: $instance->foo = 'bar';
     * 
     * @param $name Name of the property.
     * @param $value Value of the property.
     * @return void 
     **/
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }


    /**
     * Gets datas from the property.
     * Example: echo $instance->foo;
     * 
     * @param $name Name of the property to get.
     * @return mixed Datas stored in property.
     **/
    public function __get($name)
    {
        if($this->__isset($name))
        {
            return $this->get($name);
        }
    }

    /**
     * Check if property has been set or not or has null value
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * Unset property value
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->$name);
    }
}