<?php

namespace MagicObject;

use MagicObject\Util\PicoAnnotationParser;
use ReflectionClass;
use stdClass;

class Getter extends stdClass
{
    /**
     * Class parameter
     *
     * @var array
     */
    private $classParams = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $jsonAnnot = new PicoAnnotationParser(get_class($this));
        $params = $jsonAnnot->getParameters();
        foreach($params as $paramName=>$paramValue)
        {
            $vals = $jsonAnnot->parseKeyValue($paramValue);
            $this->classParams[$paramName] = $vals;
        }
    }
    
    /**
     * Load data to object
     * @param mixed $data
     */
    public function loadData($data)
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $value) {
                $key2 = $this->camelize(str_replace("-", "_", $key));
                $this->{$key2} = $value;
            }
        }
    }
    
    /**
     * Convert snake case to camel case
     *
     * @param string $input
     * @param string $separator
     * @return string
     */
    protected function camelize($input, $separator = '_')
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

    protected function snakeize($input, $glue = '_') {
        return ltrim(
            preg_replace_callback('/[A-Z]/', function ($matches) use ($glue) {
                return $glue . strtolower($matches[0]);
            }, $input),
            $glue
        );
    }

    /**
     * Get property value
     *
     * @param string $propertyName
     * @return mixed|null
     */
    public function get($propertyName)
    {
        $var = lcfirst($propertyName);
        $var = $this->camelize($var);
        return isset($this->$var) ? $this->$var : null;   
    }

    /**
     * Get value
     */
    public function value($snakeCase = false)
    {
        $parentProps = $this->propertyList(true, true);
        $value = new stdClass;
        foreach ($this as $key => $val) {
            if(!in_array($key, $parentProps))
            {
                $value->$key = $val;
            }
        }
        if($snakeCase)
        {
            $value2 = new stdClass;
            foreach ($value as $key => $val) {
                $key2 = $this->snakeize($key);
                $value2->$key2 = $val;
            }
            return $value2;
        }
        return $value;
    }

    /**
     * Property list
     * @var bool $reflectSelf
     * @return array
     */
    protected function propertyList($reflectSelf = false, $asArrayProps = false)
    {
        $reflectionClass = $reflectSelf ? self::class : get_called_class();
        $class = new ReflectionClass($reflectionClass);

        // filter only the calling class properties
        $properties = array_filter(
            $class->getProperties(), 
            function($property) use($class) { 
                return $property->getDeclaringClass()->getName() == $class->getName();
            }
        );

        if($asArrayProps)
        {
            $result = array();
            foreach ($properties as $key) {
                $prop = $key->name;
                $result[] = $prop;
            }
            return $result;
        }
        else
        {
            return $properties;
        }
    }

    /**
     * Magic method called when user call any undefined method
     *
     * @param string $method
     * @param string $params
     * @return mixed|null
     */
    public function __call($method, $params) // NOSONAR
    {
        if (strncasecmp($method, "get", 3) === 0) {
            $var = lcfirst(substr($method, 3));
            return isset($this->$var) ? $this->$var : null;
        }
        else if (strncasecmp($method, "equals", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            $value = isset($this->$var) ? $this->$var : null;
            return isset($params[0]) && $params[0] == $value;
        }
    }

    /**
     * Check if JSON naming strategy is snake case or not
     *
     * @return bool
     */
    private function isSnake()
    {
        return isset($this->classParams['JSON']) 
            && isset($this->classParams['JSON']['property-naming-strategy']) 
            && strcasecmp($this->classParams['JSON']['property-naming-strategy'], 'SNAKE_CASE') == 0
            ;
    }

    /**
     * Check if JSON naming strategy is snake case or not
     *
     * @return bool
     */
    private function isPretty()
    {
        return isset($this->classParams['JSON']) 
            && isset($this->classParams['JSON']['prettify']) 
            && strcasecmp($this->classParams['JSON']['prettify'], 'true') == 0
            ;
    }

    public function __toString()
    {
        $obj = clone $this;
        $json_flag = 0;
        if($this->isPretty())
        {
            $json_flag |= JSON_PRETTY_PRINT;
        }
        return json_encode($obj->value($this->isSnake()), $json_flag);
    }
    
    
}