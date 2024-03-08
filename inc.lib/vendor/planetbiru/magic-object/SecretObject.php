<?php

namespace MagicObject;

use MagicObject\Util\PicoAnnotationParser;
use MagicObject\Util\PicoEnvironmentVariable;
use MagicObject\Secret\PicoSecret;
use ReflectionClass;
use stdClass;
use Symfony\Component\Yaml\Yaml;

class SecretObject extends stdClass //NOSONAR
{
    const KEY_NAME = "name";
    const KEY_VALUE = "value";
    
    const KEY_PROPERTY_TYPE = "propertyType";
    const KEY_DEFAULT_VALUE = "default_value";
    
    const ANNOTATION_ENCRYPT_IN = "EncryptIn";
    const ANNOTATION_DECRYPT_IN = "DecryptIn";
    const ANNOTATION_ENCRYPT_OUT = "EncryptOut";
    const ANNOTATION_DECRYPT_OUT = "DecryptOut";
    
    /**
     * List of propertis to be encrypted when call SET
     *
     * @var string[]
     */
    private $encryptInProperties = array();
    
    /**
     * Class parameters
     *
     * @var array
     */
    protected $classParams = array();
    
    /**
     * NULL properties
     *
     * @var array
     */
    protected $nullProperties = array();
    
    /**
     * List of propertis to be decrypted when call GET
     *
     * @var string[]
     */
    private $decryptOutProperties = array();

    /**
     * List of propertis to be encrypted when call GET
     *
     * @var string[]
     */
    private $encryptOutProperties = array();
    
    /**
     * List of propertis to be decrypted when call SET
     *
     * @var string[]
     */
    private $decryptInProperties = array();

    /**
     * Read only
     *
     * @var boolean
     */
    private $readonly = false;
    
    private $secureFunction = null;
    
    /**
     * Get secure
     *
     * @return string
     */
    private function getSecure()
    {
        if($this->secureFunction != null && is_callable($this->secureFunction))
        {
            return call_user_func($this->secureFunction);
        }
        else
        {
            return PicoSecret::RANDOM_KEY_1.PicoSecret::RANDOM_KEY_2;
        }
    }
    
    /**
     * Constructor
     *
     * @param self|array|object $data
     */
    public function __construct($data = null, $secureCallback = null)
    {
        $this->_objectInfo();
        // set callback secure before load default data
        if($secureCallback != null && is_callable($secureCallback))
        {
            $this->secureFunction = $secureCallback;
        }
        if($data != null)
        {
            $this->loadData($data);
        }
    }
    
    /**
     * Process object information
     *
     * @return void
     */
    private function _objectInfo()
    {
        $className = get_class($this);
        $reflexClass = new PicoAnnotationParser($className);
        $props = $reflexClass->getProperties();
        
        // iterate each properties of the class
        foreach($props as $prop)
        {
            $reflexProp = new PicoAnnotationParser($className, $prop->name, 'property');
            $parameters = $reflexProp->getParameters();

            // add property list to be encryped or decrypted
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_ENCRYPT_IN) == 0)
                {
                    $this->encryptInProperties[] = $prop->name;
                }
                else if(strcasecmp($param, self::ANNOTATION_DECRYPT_OUT) == 0)
                {
                    $this->decryptOutProperties[] = $prop->name;
                }
                else if(strcasecmp($param, self::ANNOTATION_ENCRYPT_OUT) == 0)
                {
                    $this->encryptOutProperties[] = $prop->name;
                }
                else if(strcasecmp($param, self::ANNOTATION_DECRYPT_IN) == 0)
                {
                    $this->decryptInProperties[] = $prop->name;
                }
            }
        }
    }
    
    /**
     * Magic method
     *
     * @param string $method
     * @param mixed $params
     * @return self|bool|mixed|null
     */
    public function __call($method, $params) // NOSONAR
    {
        if (strncasecmp($method, "is", 2) === 0) {
            $var = lcfirst(substr($method, 2));
            return isset($this->$var) ? $this->$var == 1 : false;
        } else if (strncasecmp($method, "get", 3) === 0) {
            $var = lcfirst(substr($method, 3));
            return $this->_get($var);
        }
        else if (strncasecmp($method, "set", 3) === 0 && !$this->readonly) {
            $var = lcfirst(substr($method, 3));
            $this->_set($var, $params[0]);
            $this->modifyNullProperties($var, $params[0]);
            return $this;
        }
    }
    
    /**
     * Set value
     *
     * @param string $var
     * @param mixed $value
     * @return void
     */
    private function _set($var, $value)
    {
        if($this->needInputEncryption($var))
        {
            $value = $this->encryptValue($value, $this->getSecure());
        }
        else if($this->needInputDecryption($var))
        {
            $value = $this->decryptValue($value, $this->getSecure());
        }
        $this->$var = $value;
    }
    
    /**
     * Get value
     *
     * @param string $var
     * @return mixed
     */
    private function _get($var)
    {
        $value = $this->_getValue($var);
        if($this->needOutputEncryption($var))
        {
            $value = $this->encryptValue($value, $this->getSecure());
        }
        else if($this->needOutputDecryption($var))
        {
            $value = $this->decryptValue($value, $this->getSecure());
        }
        return $value;
    }
    
    /**
     * Get value
     *
     * @param string $var
     * @return mixed
     */
    private function _getValue($var)
    {
        return isset($this->$var) ? $this->$var : null;
    }
    
    /**
     * Encrypt data
     *
     * @param string $plaintext
     * @param string $hexKey
     * @return string
     */
    private function encryptValue($plaintext, $hexKey) 
    {
        $key = $hexKey;
        $method = "AES-256-CBC";
        $iv = openssl_random_pseudo_bytes(16);   
        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);
        return base64_encode($iv . $hash . $ciphertext);
    }
    
    /**
     * Decrypt data
     *
     * @param string $ciphertext
     * @param string $hexKey
     * @return string
     */
    private function decryptValue($ciphertext, $hexKey) 
    {
        $ivHashCiphertext = base64_decode($ciphertext);
        $key = $hexKey;
        $method = "AES-256-CBC";
        $iv = substr($ivHashCiphertext, 0, 16);
        $hash = substr($ivHashCiphertext, 16, 32);
        $ciphertext = substr($ivHashCiphertext, 48);
        if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) 
        {
            return null;
        }
        return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
    }
    
    /**
     * Check if value is required to be encrypted before stored
     *
     * @param string $var
     * @return bool
     */
    private function needInputEncryption($var)
    {
        return in_array($var, $this->encryptInProperties);
    }
    
    /**
     * Check if value is required to be decrypted after read
     *
     * @param string $var
     * @return bool
     */
    private function needOutputDecryption($var)
    {
        return in_array($var, $this->decryptOutProperties);
    }
    
    /**
     * Check if value is required to be encrypted after read
     *
     * @param string $var
     * @return bool
     */
    private function needOutputEncryption($var)
    {
        return in_array($var, $this->encryptOutProperties);
    }
    
    /**
     * Check if value is required to be decrypted before stored
     *
     * @param string $var
     * @return bool
     */
    private function needInputDecryption($var)
    {
        return in_array($var, $this->decryptInProperties);
    }

    /**
     * Load data to object
     * @param mixed $data
     * @return self
     */
    public function loadData($data)
    {
        if($data != null)
        {
            if($data instanceof self)
            {
                $values = $data->value();
                foreach ($values as $key => $value) {
                    $key2 = $this->camelize($key);
                    $this->_set($key2, $value);
                }
            }
            else if (is_array($data) || is_object($data)) {
                foreach ($data as $key => $value) {
                    $key2 = $this->camelize($key);
                    $this->_set($key2, $value);
                }
            }
        }
        return $this;
    }

    /**
     * Load data from INI file
     *
     * @param string $path
     * @param bool $systemEnv
     * @return self
     */
    public function loadIniFile($path, $systemEnv = false)
    {
        // Parse without sections
        $data = parse_ini_file($path);
        if($systemEnv)
        {
            $env = new PicoEnvironmentVariable();
            $data = $env->replaceSysEnvAll($data, true);
        }
        $this->loadData($data);
        return $this;
    }

    /**
     * Load data from Yaml file
     *
     * @param string $path
     * @param bool $systemEnv
     * @return self
     */
    public function loadYamlFile($path, $systemEnv = false, $asObject = false)
    {
        $data = Yaml::parseFile($path);
        if($systemEnv)
        {
            $env = new PicoEnvironmentVariable();
            $data = $env->replaceSysEnvAll($data, true);
        }
        if($asObject)
        {
            // convert to object
            $obj = json_decode(json_encode((object) $data), false);
            $this->loadData($obj);
        }
        else
        {
            $this->loadData($data);
        }
        return $this;
    }

    /**
     * Load data from JSON file
     *
     * @param string $path
     * @param bool $systemEnv
     * @return self
     */
    public function loadJsonFile($path, $systemEnv = false, $asObject = false)
    {
        $data = json_decode(file_get_contents($path));
        if($systemEnv)
        {
            $env = new PicoEnvironmentVariable();
            $data = $env->replaceSysEnvAll($data, true);
        }
        if($asObject)
        {
            // convert to object
            $obj = json_decode(json_encode((object) $data), false);
            $this->loadData($obj);
        }
        else
        {
            $this->loadData($data);
        }
        return $this;
    }
    
    /**
     * Set property value
     *
     * @param string $propertyName
     * @param mixed|null
     * @param bool $skipModifyNullProperties
     * @return self
     */
    public function set($propertyName, $propertyValue, $skipModifyNullProperties = false)
    {
        $var = lcfirst($propertyName);
        $var = $this->camelize($var);
        $this->{$var} = $propertyValue;
        if(!$skipModifyNullProperties && $propertyValue === null)
        {
            $this->modifyNullProperties($var, $propertyValue);
        }
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
        $var = lcfirst($propertyName);
        $var = $this->camelize($var);
        return isset($this->$var) ? $this->$var : null;
    }
    
    /**
     * Get property value 
     *
     * @param string $propertyName
     * @return mixed|null
     */
    public function getOrDefault($propertyName, $defaultValue = null)
    {
        $var = lcfirst($propertyName);
        $var = $this->camelize($var);
        return isset($this->$var) ? $this->$var : $defaultValue;
    }
    
    /**
     * Copy value from other object
     *
     * @param self|mixed $source
     * @param array $filter
     * @param bool $includeNull
     * @return void
     */
    public function copyValueFrom($source, $filter = null, $includeNull = false)
    {
        if($filter != null)
        {
            $tmp = array();
            $index = 0;
            foreach($filter as $val)
            {
                $tmp[$index] = trim($this->camelize($val));               
                $index++;
            }
            $filter = $tmp;
        }
        $values = $source->value();
        foreach($values as $property=>$value)
        {
            if(
                ($filter == null || (is_array($filter) && !empty($filter) && in_array($property, $filter))) 
                && 
                ($includeNull || $value != null)
                )
            {
                $this->set($property, $value);
            }
        }
    }
    
    /**
     * Fix value
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    protected function fixValue($value, $type) // NOSONAR
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
     * Get object value
     * @return stdClass
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
     * Get object value
     * @return stdClass
     */
    public function valueObject($snakeCase = false)
    {
        return $this->value($snakeCase);
    }

    /**
     * Get object value as associative array
     * @return array
     */
    public function valueArray($snakeCase = false)
    {
        $value = $this->value($snakeCase);
        return json_decode(json_encode($value), true);
    }
    
    /**
     * Get object value as associated array with upper case first
     *
     * @return array
     */
    public function valueArrayUpperCamel()
    {
        $obj = clone $this;
        $array = (array) $obj->value();
        $renameMap = array();
        $keys = array_keys($array);
        foreach($keys as $key)
        {
            $renameMap[$key] = ucfirst($key);
        }          
        $array = array_combine(array_map(function($el) use ($renameMap) {
            return $renameMap[$el];
        }, array_keys($array)), array_values($array));
        return $array;
    }
    
    /**
     * Check if JSON naming strategy is snake case or not
     *
     * @return bool
     */
    protected function _snake()
    {
        return isset($this->classParams['JSON'])
            && isset($this->classParams['JSON']['property-naming-strategy'])
            && strcasecmp($this->classParams['JSON']['property-naming-strategy'], 'SNAKE_CASE') == 0
            ;
    }
    
    /**
     *  Check if JSON naming strategy is upper camel case or not
     *
     * @return bool
     */
    protected function isUpperCamel()
    {
        return isset($this->classParams['JSON'])
            && isset($this->classParams['JSON']['property-naming-strategy'])
            && strcasecmp($this->classParams['JSON']['property-naming-strategy'], 'UPPER_CAMEL_CASE') == 0
            ;
    }
    
    /**
     * Check if JSON naming strategy is camel case or not
     *
     * @return bool
     */
    protected function _camel()
    {
        return !$this->_snake();
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

    /**
     * Convert camel case to snake case
     *
     * @param string $input
     * @param string $glue
     * @return string
     */
    protected function snakeize($input, $glue = '_') {
        return ltrim(
            preg_replace_callback('/[A-Z]/', function ($matches) use ($glue) {
                return $glue . strtolower($matches[0]);
            }, $input),
            $glue
        );
    } 
    
    
    /**
     * Property list
     * @var bool $reflectSelf
     * @var bool $asArrayProps
     * @return array
     */
    protected function propertyList($reflectSelf = false, $asArrayProps = false)
    {
        $reflectionClass = $reflectSelf ? self::class : get_called_class();
        $class = new ReflectionClass($reflectionClass);

        // filter only the calling class properties
        // skip parent properties
        $properties = array_filter(
            $class->getProperties(),
            function($property) use($class) {
                return $property->getDeclaringClass()->getName() == $class->getName();
            }
        );
        if($asArrayProps)
        {
            $result = array();
            $index = 0;
            foreach ($properties as $key) {
                $prop = $key->name;
                $result[$index] = $prop;
                
                $index++;
            }
            return $result;
        }
        else
        {
            return $properties;
        }
    }
    
    /**
     * Modify null properties
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return void
     */
    private function modifyNullProperties($propertyName, $propertyValue)
    {
        if($propertyValue === null && !isset($this->nullProperties[$propertyName]))
        {
            $this->nullProperties[$propertyName] = true; 
        }
        if($propertyValue != null && isset($this->nullProperties[$propertyName]))
        {
            unset($this->nullProperties[$propertyName]); 
        }
    }
    
    /**
     * Get encrypted value
     *
     * @return array
     */
    public function encryptedValue()
    {
        $obj = clone $this;
        $obj = $this->encryptValueRecorsive($obj);
        $array = json_decode(json_encode($obj->value($this->isSnake())), true);
        return $this->encryptValueRecursive($array);
    }

    /**
     * Encrypt value recursively
     *
     * @param array $array Value to be encrypted in array
     * @return array
     */
    private function encryptValueRecursive($array)
    {
        foreach($array as $key=>$val)
        {
            if(is_array($val))
            {
                $array[$key] = $this->encryptValueRecursive($val);
            }
            else if(is_string($val))
            {
                $array[$key] = $this->encryptValue($val, $this->getSecure());
            }
        }
        return $array;
    }
    
    /**
     * Convert object to string
     *
     * @return string
     */
    public function __toString()
    {
        $obj = clone $this;
        return json_encode($obj->value($this->isSnake()));
    }
}