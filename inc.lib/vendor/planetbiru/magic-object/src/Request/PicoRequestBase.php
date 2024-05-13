<?php

namespace MagicObject\Request;

use MagicObject\Exceptions\InvalidAnnotationException;
use MagicObject\MagicObject;
use MagicObject\Util\ClassUtil\PicoAnnotationParser;
use MagicObject\Util\PicoStringUtil;
use ReflectionClass;
use stdClass;

class PicoRequestBase extends stdClass //NOSONAR
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
            if(is_array($paramValue))
            {
                throw new InvalidAnnotationException("Invalid annotation @".$paramName);
            }
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
                $key2 = PicoStringUtil::camelize(str_replace("-", "_", $key));
                $this->{$key2} = $value;
            }
        }
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
     * @param array $params
     * @return mixed|null
     */
    public function get($propertyName, $params = null)
    {
        $var = PicoStringUtil::camelize($propertyName);
        $value = isset($this->$var) ? $this->$var : null;
        if(isset($params) && !empty($params))
        {
            $filter = $params[0];
            return $this->filterValue($value, $filter);
        }
        else
        {
            return $value;
        }
    }

    /**
     * Get value
     *
     * @var boolean $snakeCase
     */
    public function value($snakeCase = false)
    {
        $parentProps = $this->propertyList(true, true);
        $value = new stdClass;
        foreach ($this as $key => $val)
        {
            if(!in_array($key, $parentProps))
            {
                $value->$key = $val;
            }
        }
        if($snakeCase)
        {
            $value2 = new stdClass;
            foreach ($value as $key => $val)
            {
                $key2 = PicoStringUtil::snakeize($key);
                $value2->$key2 = $val;
            }
            return $value2;
        }
        return $value;
    }

    /**
     * Property list
     *
     * @var boolean $reflectSelf
     * @var boolean $asArrayProps
     * @return array
     */
    protected function propertyList($reflectSelf = false, $asArrayProps = false)
    {
        $reflectionClass = $reflectSelf ? self::class : get_called_class();
        $class = new ReflectionClass($reflectionClass);

        // filter only the calling class properties
        $properties = array_filter(
            $class->getProperties(),
            function($property) use($class)
            {
                return $property->getDeclaringClass()->getName() == $class->getName();
            }
        );

        if($asArrayProps)
        {
            $result = array();
            foreach ($properties as $key)
            {
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
     * Filter input
     *
     * @param integer $type
     * @param string $variable_name
     * @param integer $filter
     * @param boolean $escapeSQL
     * @return mixed
     */
    public function filterInput($type, $variable_name, $filter = PicoFilterConstant::FILTER_DEFAULT, $escapeSQL=false) // NOSONAR
    {
        $var = array();
        switch ($type) {
            case INPUT_GET:
                $var = $_GET;
                break;
            case INPUT_POST:
                $var = $_POST;
                break;
            case INPUT_COOKIE:
                $var = $_COOKIE;
                break;
            case INPUT_SERVER:
                $var = $_SERVER;
                break;
            case INPUT_ENV:
                $var = $_ENV;
                break;
            default:
                $var = $_GET;
        }
        return $this->filterValue(isset($var[$variable_name])?$var[$variable_name]:null, $filter, $escapeSQL);
    }
    
    /**
     * Filter value
     *
     * @param mixed $val
     * @param integer $filter
     * @param boolean $escapeSQL
     * @param boolean $nullIfEmpty
     * @return mixed|null
     */
    public function filterValue($val, $filter = PicoFilterConstant::FILTER_DEFAULT, $escapeSQL = false, $nullIfEmpty = false)
    {
        $ret = null;
        if(is_scalar($val))
        {
            return $this->filterValueSingle($val, $filter, $escapeSQL, $nullIfEmpty);
        }
        else if(is_array($val))
        {
            $ret = array();
            foreach($val as $k=>$v)
            {
                $ret[$k] = $this->filterValueSingle($v, $filter, $escapeSQL, $nullIfEmpty);
            }
        }
        else if(is_object($val))
        {
            $ret = new stdClass();
            foreach($val as $k=>$v)
            {
                $ret->{$k} = $this->filterValueSingle($v, $filter, $escapeSQL, $nullIfEmpty);
            }
        }
        return $ret;  
    }

    /**
     * Filter single value
     *
     * @param mixed $val
     * @param integer $filter
     * @param boolean $escapeSQL
     * @param boolean $nullIfEmpty
     * @return mixed
     */
    public function filterValueSingle($val, $filter = PicoFilterConstant::FILTER_DEFAULT, $escapeSQL = false, $nullIfEmpty = false) //NOSONAR
    {
        // add filter
        if($filter == PicoFilterConstant::FILTER_SANITIZE_EMAIL)
        {
            $val = trim(strtolower($val));
            $val = filter_var($val, PicoFilterConstant::FILTER_VALIDATE_EMAIL);
            if($val === false)
            {
                $val = "";
            }
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_URL)
        {
            // filter url
            $val = trim($val);
            if(stripos($val, "://") === false && strlen($val)>2)
            {
                $val = "http://".$val;
            }
            $val = filter_var($val, FILTER_VALIDATE_URL);
            if($val === false)
            {
                $val = "";
            }
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_ALPHA){
            $val = preg_replace("/[^A-Za-z]/i", "", $val); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_ALPHANUMERIC){

            $val = preg_replace("/[^A-Za-z\d]/i", "", $val); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_ALPHANUMERICPUNC){
            $val = preg_replace("/[^A-Za-z\.\-\d_]/i", "", $val); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NUMBER_FLOAT){
            $val = preg_replace("/[^Ee\+\-\.\d]/i", "", $val); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT){
            $val = preg_replace("/[^\+\-\d]/i", "", $val); // NOSONAR
            if(empty($val))
            {
                $val = 0;
            }
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NUMBER_UINT){
            $val = preg_replace("/[^\+\-\d]/i", "", $val); // NOSONAR
            if(empty($val))
            {
                $val = 0;
            }
            $val = abs($val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NUMBER_OCTAL){
            $val = preg_replace("/[^0-7]/i", "", $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NUMBER_HEXADECIMAL){
            $val = preg_replace("/[^A-Fa-f\d]/i", "", $val); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_COLOR){
            $val = preg_replace("/[^A-Fa-f\d]/i", "", $val); // NOSONAR
            if(strlen($val) < 3){
                $val = "";
            }
            else if(strlen($val) > 3 && strlen($val) != 3 && strlen($val) < 6){
                $val = substr($val, 0, 3);
            }
            else if(strlen($val) > 6){
                $val = substr($val, 0, 6);
            }
            if(strlen($val) >= 3){
                $val = strtoupper("#".$val);
            }
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_NO_DOUBLE_SPACE){
            $val = trim(preg_replace("/\s+/"," ",$val)); // NOSONAR
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_PASSWORD){
            $val = trim(preg_replace("/\s+/"," ",$val)); // NOSONAR
            $val = str_ireplace(array('"',"'","`","\\","\0","\r","\n","\t"), "", $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS){
            $val = htmlspecialchars($val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_ENCODED){
            $val = rawurlencode($val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_STRING_NEW){
            $val = trim(strip_tags($val),"\r\n\t ");
            $val = str_replace(array('<','>','"'), array('&lt;','&gt;','&quot;'), $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_STRING_INLINE){
            $val = trim(strip_tags($val),"\r\n\t ");
            $val = str_replace(array('<','>','"'), array('&lt;','&gt;','&quot;'), $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_STRING_BASE64){
            $val = preg_replace("/[^A-Za-z0-9\+\/\=]/", "", $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_POINT){
            $val = preg_replace("/[^0-9\-\+\/\.,]/", "", $val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_IP){
            $val = filter_var($val, FILTER_VALIDATE_IP);
            if($val === false)
            {
                $val = "";
            }
        }
        if(
            $escapeSQL && 
            (
                $filter == PicoFilterConstant::FILTER_SANITIZE_EMAIL ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_ENCODED ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_IP ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_NO_DOUBLE_SPACE ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_STRING_NEW ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_STRING_INLINE ||
                $filter == PicoFilterConstant::FILTER_SANITIZE_URL
            )
            )
        {
            $val = $this->addslashes($val);
        }
        if($filter == PicoFilterConstant::FILTER_SANITIZE_BOOL){
            $val = trim(preg_replace("/\s+/"," ",$val)); // NOSONAR
            return ($val != null && !empty($val)) && ($val === true || $val == 1 || $val == "1"); 
        }

        if(is_string($val) && empty($val) && $nullIfEmpty)
        {
            return null;
        }
        return $val;
    }

    /**
     * Addslahes
     *
     * @param string $inp
     * @return string
     */
    public function addslashes($inp)
    {
        return addslashes($inp);
    }

    /**
     * Get value from formated number
     *
     * @param stdClass|MagicObject $cfg
     * @param mixed $input
     * @return float
     */
    public function _getValue($cfg, $input)
    {
        if($input === null || $input == '' || $input == 'undefined')
        {
            return 0;
        }
        $output = $input;

        $decimal_separator = $cfg->getAppDecimalSeparator();
        $thousand_separator = $cfg->getAppThousandSeparator();

        if($thousand_separator != "")
        {
            $output = str_replace($thousand_separator, '', $output);
        }
        if($decimal_separator != ".")
        {
            $output = str_replace(".", "_", $output);
            $output = str_replace(",", ".", $output);
            $output = str_replace("_", "", $output);
        }
        if(empty($output))
        {
            return 0;
        }
        // force value to numeric
        return $output * 1;
    }

    /**
     * Get value from formated number
     *
     * @param stdClass|MagicObject $cfg
     * @param mixed $input
     * @return float
     */
    public function getValue($cfg, $input)
    {
        return $this->_getValue($cfg, $input);
    }

    /**
     * Magic method called when user call any undefined method
     *
     * @param string $method
     * @param array $params
     * @return mixed|null
     */
    public function __call($method, $params) //NOSONAR
    {
        
        if (strncasecmp($method, "countable", 9) === 0) 
        {
            $var = lcfirst(substr($method, 9));
            return is_array($this->$var);
        } 
        else if (strncasecmp($method, "isset", 5) === 0) 
        {
            $var = lcfirst(substr($method, 5));
            return isset($this->$var);
        } 
        else if (strncasecmp($method, "is", 2) === 0) 
        {
            $var = lcfirst(substr($method, 2));
            return isset($this->$var) ? $this->$var == 1 : false;
        }
        else if (strncasecmp($method, "get", 3) === 0)
        {
            $var = lcfirst(substr($method, 3));
            return $this->get($var, $params);
        }
        else if (strncasecmp($method, "set", 3) === 0)
        {
            $var = lcfirst(substr($method, 3));
            $this->$var = $params[0];
            return $this;
        }
        else if (strncasecmp($method, "equals", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            $value = isset($this->$var) ? $this->$var : null;
            return isset($params[0]) && $params[0] == $value;
        }
        else if (strncasecmp($method, "checkbox", 8) === 0) {
            $var = lcfirst(substr($method, 8));
            $this->$var = isset($this->$var) ? $this->$var : $params[0];
            return $this;
        }
        else if (strncasecmp($method, "filter", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            if(isset($this->$var))
            {
                $this->$var = $this->applyFilter($this->$var, $params[0]);
            }
            return $this;
        }
        else if (strncasecmp($method, "createSelected", 14) === 0) {
            $var = lcfirst(substr($method, 14));
            if(isset($this->$var))
            {
                return $this->$var == $params[0] ? ' selected="selected"' : '';
            }
        }
        else if (strncasecmp($method, "createChecked", 13) === 0) {
            $var = lcfirst(substr($method, 13));
            if(isset($this->$var))
            {
                return $this->$var == $params[0] ? ' checked="checked"' : '';
            }
        }
        else if (strncasecmp($method, "unset", 5) === 0) {
            $var = lcfirst(substr($method, 5));
            $this->removeValue($var, $params[0]);
            return $this;
        }
    }  
    private function applyFilter($value, $filterType)
    {
        if(isset($value))
        {
            if($filterType == PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS)
            {
                $ret = htmlspecialchars($value);
            }
            else if($filterType == PicoFilterConstant::FILTER_SANITIZE_BOOL)
            {
                $ret = $value === true || $value == 1 || $value == "1";
            }
            else
            {
                $ret = $value;
            }
            return $ret;
        }
        return null;
    }

    /**
     * Check if JSON naming strategy is snake case or not
     *
     * @return boolean
     */
    private function isSnake()
    {
        return isset($this->classParams['JSON'])
            && isset($this->classParams['JSON']['property-naming-strategy'])
            && strcasecmp($this->classParams['JSON']['property-naming-strategy'], 'SNAKE_CASE') == 0
            ;
    }
    
    /**
     * Check if JSON naming strategy is camel case or not
     *
     * @return boolean
     */
    protected function isCamel()
    {
        return !$this->isSnake();
    }

    /**
     * Check if JSON naming strategy is snake case or not
     *
     * @return boolean
     */
    private function isPretty()
    {
        return isset($this->classParams['JSON'])
            && isset($this->classParams['JSON']['prettify'])
            && strcasecmp($this->classParams['JSON']['prettify'], 'true') == 0
            ;
    }

    /**
     * Check if request is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->value(false));
    }

    /**
     * toString
     *
     * @return string
     */
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