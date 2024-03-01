<?php

namespace MagicObject;

use Exception;
use PDOException;
use PDOStatement;
use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabasePersistent;
use MagicObject\Database\PicoDatabaseStructure;
use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPageData;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Exceptions\NoDatabaseConnectionException;
use MagicObject\Exceptions\NoRecordFoundException;
use MagicObject\Util\PicoAnnotationParser;
use MagicObject\Util\PicoEnvironmentVariable;
use ReflectionClass;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Class to create magic object. 
 * Magic object is an object created from any class so that user can add any property with any name and value, load data from INI file, Yaml file, JSON file and database. 
 * User can create entity from a table of database, insert, select, update and delete record from database. 
 * User can also create property from other entity with full name of class (namespace + class name)
 */
class MagicObject extends stdClass // NOSONAR
{
    const MESSAGE_NO_DATABASE_CONNECTION = "No database connection provided";
    const MESSAGE_NO_RECORD_FOUND = "No record found";
    const KEY_PROPERTY_TYPE = "propertyType";
    const KEY_DEFAULT_VALUE = "default_value";
    const KEY_NAME = "name";
    const KEY_VALUE = "value";
    
    /**
     * Flag readonly
     *
     * @var bool
     */
    private $readonly = false; // NOSONAR

    /**
     * Database connection
     *
     * @var PicoDatabase
     */
    private $database; // NOSONAR
    /**
     * Class params
     *
     * @var array
     */
    private $classParams = array();

    /**
     * Null properties
     *
     * @var array
     */
    private $nullProperties = array();

    /**
     * Get null properties
     *
     * @return array
     */
    public function nullPropertiyList()
    {
        return $this->nullProperties;
    }

    /**
     * Constructor
     *
     * @param self|array|object $data
     * @param PicoDatabase $database
     */
    public function __construct($data = null, $database = null)
    {
        $jsonAnnot = new PicoAnnotationParser(get_class($this));
        $params = $jsonAnnot->getParameters();
        foreach($params as $paramName=>$paramValue)
        {
            $vals = $jsonAnnot->parseKeyValue($paramValue);
            $this->classParams[$paramName] = $vals;
        }
        if($data != null)
        {
            $this->loadData($data);
        }
        if($database != null)
        {
            $this->database = $database;
        }
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
                    $this->set($key2, $value, true);
                }
            }
            else if (is_array($data) || is_object($data)) {
                foreach ($data as $key => $value) {
                    $key2 = $this->camelize($key);
                    $this->set($key2, $value, true);
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
     * Set readonly. When object is set to readonly, setter will not change value of its properties but loadData still works fine
     *
     * @param bool $readonly
     * @return self
     */
    protected function readOnly($readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set database connection
     * @var PicoDatabase $database
     * @return self
     */
    public function withDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Remove property
     *
     * @param object $sourceData
     * @param array $propertyNames
     * @return mixed
     */
    public function removePropertyExcept($sourceData, $propertyNames)
    {
        if(is_object($sourceData))
        {
            // iterate
            $resultData = new stdClass;
            foreach($sourceData as $key=>$val)
            {
                if(in_array($key, $propertyNames))
                {
                    $resultData->$key = $val;
                }
            }
            return $resultData;
        }
        if(is_array($sourceData))
        {
            // iterate
            $resultData = array();
            foreach($sourceData as $key=>$val)
            {
                if(in_array($key, $propertyNames))
                {
                    $resultData[$key] = $val;
                }
            }
            return $resultData;
        }
        return new stdClass;
    }

    /**
     * Save to database
     * @param bool $includeNull If TRUE, all column will be saved to database include null. If FALSE, only column with not null value will be saved to database
     * @return PDOStatement
     * NoDatabaseConnectionException|NoRecordFoundException|PDOException
     */
    public function save($includeNull = false)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->save($includeNull);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Select data from database
     *
     * @return self
     * @throws NoDatabaseConnectionException|NoRecordFoundException|PDOException
     */
    public function select()
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            $data = $persist->select();
            if($data == null)
            {
                throw new NoRecordFoundException(self::MESSAGE_NO_RECORD_FOUND);
            }
            $this->loadData($data);
            return $this;
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Insert into database
     *
     * @param bool $includeNull If TRUE, all column will be saved to database include null. If FALSE, only column with not null value will be saved to database
     * @return PDOStatement
     * @throws NoDatabaseConnectionException|PDOException
     */
    public function insert($includeNull = false)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->insert($includeNull);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Update data on database
     *
     * @param bool $includeNull If TRUE, all column will be saved to database include null. If FALSE, only column with not null value will be saved to database
     * @return PDOStatement
     * @throws NoDatabaseConnectionException|PDOException
     */
    public function update($includeNull = false)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->update($includeNull);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Delete data from database
     *
     * @return PDOStatement
     * @throws NoDatabaseConnectionException|PDOException
     */
    public function delete()
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->delete();
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }
    
    public function showCreateTable($databaseType, $tableName = null)
    {
        $structure = new PicoDatabaseStructure($this);
        return $structure->showCreateTable($databaseType, $tableName);
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
     * Unset property value
     *
     * @param string $propertyName
     * @param bool $skipModifyNullProperties
     * @return self
     */
    private function removeValue($propertyName, $skipModifyNullProperties = false)
    {
        return $this->set($propertyName, null, $skipModifyNullProperties);
    }
    
    /**
     * Get default value
     *
     * @param bool $snakeCase
     * @return stdClass
     */
    public function defatultValue($snakeCase = false)
    {
        $persist = new PicoDatabasePersistent($this->database, $this);
        $tableInfo = $persist->getTableInfo();
        $defaultValue = new stdClass;
        if(isset($tableInfo->defaultValue))
        {
            foreach($tableInfo->defaultValue as $column)
            {
                if(isset($column[self::KEY_NAME]))
                {
                    $columnName = trim($column[self::KEY_NAME]);
                    if($snakeCase)
                    {
                        $col = $this->snakeize($columnName);
                    }
                    else
                    {
                        $col = $columnName;
                    }
                    $defaultValue->$col = $persist->fixData($column[self::KEY_VALUE], $column[self::KEY_PROPERTY_TYPE]);
                }
            }
        }
        return $defaultValue;
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
     * Check if JSON naming strategy is snake case or not
     *
     * @return bool
     */
    protected function _pretty()
    {
        return isset($this->classParams['JSON'])
            && isset($this->classParams['JSON']['prettify'])
            && strcasecmp($this->classParams['JSON']['prettify'], 'true') == 0
            ;
    }
    
    /**
     * Check if data is not null and not empty
     *
     * @param mixed $value
     * @return bool
     */
    private function _notNullAndNotEmpty($value)
    {
        return $value != null && !empty($value);
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
     * List all
     *
     * @param PicoSpecification $specification
     * @param PicoPagable|string $pagable
     * @return PicoPageData
     * @throws NoRecordFoundException if no record found
     * @throws NoDatabaseConnectionException if no database connection
     */
    public function listAll($specification = null, $pagable = null)
    {
        return $this->findAll($specification, $pagable, true);
    }

    /**
     * Find all
     *
     * @param PicoSpecification $specification
     * @param PicoPagable|string $pagable
     * @param PicoSortable|string $sortable
     * @param bool $passive
     * @return PicoPageData
     * @throws NoRecordFoundException if no record found
     * @throws NoDatabaseConnectionException if no database connection
     */
    public function findAll($specification = null, $pagable = null, $sortable = null, $passive = false)
    {
        $startTime = microtime(true);
        try
        {
            $pageData = null;
            if($this->database != null && $this->database->isConnected())
            {
                $persist = new PicoDatabasePersistent($this->database, $this);
                $result = $persist->findAll($specification, $pagable, $sortable);
                $match = $persist->countAll($specification);
                if($this->_notNullAndNotEmpty($result))
                {
                    $pageData = new PicoPageData($this->toArrayObject($result, $passive), $pagable, $match, $startTime);
                }
                else
                {
                    $pageData = new PicoPageData(array(), $pagable, 0, $startTime);
                }
            }
            else
            {
                $pageData = new PicoPageData(array(), $pagable, 0, $startTime);
            }
            return $pageData;
        }
        catch(Exception $e)
        {
            return new PicoPageData(array(), $pagable, 0, $startTime);
        }
    }

    /**
     * Find one record by primary key value. 
     * 
     * @param array $params
     * @return self
     */
    public function find($params)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            $result = $persist->find($params);
            if($this->_notNullAndNotEmpty($result))
            {
                $this->loadData($result);
                return $this;
            }
            else
            {
                throw new NoRecordFoundException(self::MESSAGE_NO_RECORD_FOUND);
            }
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Find by params
     *
     * @param string $method
     * @param mixed $params
     * @param PicoPagable $pagable
     * @param PicoSortable|string $sortable
     * @param bool $passive
     * @return PicoPageData
     * @throws NoRecordFoundException|NoDatabaseConnectionException
     */
    private function findBy($method, $params, $pagable = null, $sortable = null, $passive = false)
    {
        $startTime = microtime(true);
        try
        {
            $pageData = null;
            if($this->database != null && $this->database->isConnected())
            {
                $persist = new PicoDatabasePersistent($this->database, $this);
                $result = $persist->findBy($method, $params, $pagable, $sortable);
                $match = $persist->countBy($method, $params);
                if($this->_notNullAndNotEmpty($result))
                {
                    $pageData = new PicoPageData($this->toArrayObject($result, $passive), $pagable, $match, $startTime);
                }
                else
                {
                    $pageData = new PicoPageData(array(), $pagable, 0, $startTime);
                }
            }
            else
            {
                $pageData = new PicoPageData(array(), $pagable, 0, $startTime);
            }
            return $pageData;
        }
        catch(Exception $e)
        {
            return new PicoPageData(array(), $pagable, 0, $startTime);
        }
    }
    
    /**
     * Count data from database
     *
     * @param string $method
     * @param mixed $params
     * @return integer
     */
    private function countBy($method, $params)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->countBy($method, $params);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }
    
    /**
     * Delete by params
     *
     * @param string $method
     * @param mixed $params
     * @return integer
     */
    private function deleteBy($method, $params)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->deleteBy($method, $params);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }

    /**
     * Find one by params
     *
     * @param string $method
     * @param mixed $params
     * @param PicoSortable|string $sortable
     * @return object
     */
    private function findOneBy($method, $params, $sortable = null)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            $result = $persist->findOneBy($method, $params, $sortable);
            if($this->_notNullAndNotEmpty($result))
            {
                $this->loadData($result);
                return $this;
            }
            else
            {
                throw new NoRecordFoundException(self::MESSAGE_NO_RECORD_FOUND);
            }
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }
    
    /**
     * Exists by params
     *
     * @param string $method
     * @param mixed $params
     * @param string $orderType
     * @return bool
     */
    private function existsBy($method, $params)
    {
        if($this->database != null && $this->database->isConnected())
        {
            $persist = new PicoDatabasePersistent($this->database, $this);
            return $persist->existsBy($method, $params);
        }
        else
        {
            throw new NoDatabaseConnectionException(self::MESSAGE_NO_DATABASE_CONNECTION);
        }
    }
    
    /**
     * Convert bool to text
     *
     * @param string $propertyName
     * @param string[] $params
     * @return string
     */
    private function booleanToTextBy($propertyName, $params)
    {
        $value = $this->get($propertyName);
        if(!isset($value))
        {
            $boolVal = false;
        }
        else
        {
            $boolVal = $value === true || $value == 1 || $value = "1"; 
        }
        return $boolVal?$params[0]:$params[1];
    }

    /**
     * Convert to array object
     *
     * @param array $result
     * @param bool $passive
     * @return array
     */
    private function toArrayObject($result, $passive = false)
    {
        $instance = array();
        $index = 0;
        foreach($result as $value)
        {
            $className = get_class($this);
            $instance[$index] = new $className($value, $passive ? null : $this->database);
            $index++;
        }
        return $instance;
    }
    
    /**
     * Get number of property of the object
     *
     * @return integer
     */
    public function size()
    {
        $parentProps = $this->propertyList(true, true);
        $length = 0;
        foreach ($this as $key => $val) {
            if(!in_array($key, $parentProps))
            {
                $length++;
            }
        }
        return $length;
    }

    /**
     * Magic method called when user call any undefined method. __call method will check the prefix of called method and call appropriated method according to its name and its parameters.
     * is &raquo; get property value as bool. Number will true if it's value is 1. String will be convert to number first. This method not require database connection.
     * get &raquo; get property value. This method not require database connection.
     * set &raquo; set property value. This method not require database connection.
     * unset &raquo; unset property value. This method not require database connection.
     * findOneBy &raquo; search data from database and return one record. This method require database connection.
     * findFirstBy &raquo; search data from database and return first record. This method require database connection.
     * findLastBy &raquo; search data from database and return last record. This method require database connection.
     * findBy &raquo; search data from database. This method require database connection.
     * findAscBy &raquo; search data from database order by primary keys ascending. This method require database connection.
     * findDescBy &raquo; search data from database order by primary keys descending. This method require database connection.
     * findAllAsc &raquo; search data from database without filter order by primary keys ascending. This method require database connection.
     * findAllDesc &raquo; search data from database without filter order by primary keys descending. This method require database connection.
     * listBy &raquo; search data from database. Similar to findBy but does not contain a connection to the database so objects cannot be saved directly to the database. This method require database connection.
     * listAscBy &raquo; search data from database order by primary keys ascending. Similar to findAscBy but does not contain a connection to the database so objects cannot be saved directly to the database. This method require database connection.
     * listDescBy &raquo; search data from database order by primary keys descending. Similar to findDescBy but does not contain a connection to the database so objects cannot be saved directly to the database. This method require database connection.
     * listAllAsc &raquo; search data from database without filter order by primary keys ascending. Similar to findAllAsc but does not contain a connection to the database so objects cannot be saved directly to the database. This method require database connection.
     * listAllDesc &raquo; search data from database without filter order by primary keys descending. Similar to findAllDesc but does not contain a connection to the database so objects cannot be saved directly to the database. This method require database connection.
     * countBy &raquo; count data from database.
     * existsBy &raquo; check data from database. This method require database connection.
     * deleteBy &raquo; delete data from database without read it first. This method require database connection.
     * booleanToTextBy &raquo; convert bool value to yes/no or true/false depend on parameters given. Example: $result = booleanToTextByActive("Yes", "No"); If $obj->active is true, $result will be "Yes" otherwise "No". This method not require database connection.
     * booleanToSelectedBy &raquo; Create attribute selected="selected" for form. This method not require database connection.
     * booleanToCheckedBy &raquo; Create attribute checked="checked" for form. This method not require database connection.
     * startsWith &raquo; Check that value starts with any string. This method not require database connection.
     * endsWith &raquo; Check that value ends with any string. This method not require database connection.
     *
     * @param string $method Method name
     * @param mixed $params Parameters
     * @return mixed|null
     */    
    public function __call($method, $params) // NOSONAR
    {
        if (strncasecmp($method, "hasValue", 8) === 0) {
            $var = lcfirst(substr($method, 8));
            return isset($this->$var);
        } 
        else if (strncasecmp($method, "is", 2) === 0) {
            $var = lcfirst(substr($method, 2));
            return isset($this->$var) ? $this->$var == 1 : false;
        } 
        else if (strncasecmp($method, "equals", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            return isset($this->$var) && $this->$var == $params[0];
        } 
        else if (strncasecmp($method, "get", 3) === 0) {
            $var = lcfirst(substr($method, 3));
            return isset($this->$var) ? $this->$var : null;
        }
        else if (strncasecmp($method, "set", 3) === 0 && !$this->readonly) {
            $var = lcfirst(substr($method, 3));
            $this->$var = $params[0];
            $this->modifyNullProperties($var, $params[0]);
            return $this;
        }
        else if (strncasecmp($method, "unset", 5) === 0 && !$this->readonly) {
            $var = lcfirst(substr($method, 5));
            $this->removeValue($var, $params[0]);
            return $this;
        }
        else if (strncasecmp($method, "findOneBy", 9) === 0) {
            $var = lcfirst(substr($method, 9));
            $sortable = $this->sortableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findOneBy($var, $parameters, $sortable);
        }
        else if (strncasecmp($method, "findFirstBy", 11) === 0) {
            $var = lcfirst(substr($method, 11));
            return $this->findOneBy($var, $params, PicoDatabasePersistent::ORDER_ASC);
        }
        else if (strncasecmp($method, "findLastBy", 10) === 0) {
            $var = lcfirst(substr($method, 10));
            return $this->findOneBy($var, $params, PicoDatabasePersistent::ORDER_DESC);
        }
        else if (strncasecmp($method, "findBy", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // get sortable
            $sortable = $this->sortableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, $sortable);
        }
        else if (strncasecmp($method, "findAscBy", 9) === 0) {
            $var = lcfirst(substr($method, 9));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, PicoDatabasePersistent::ORDER_ASC);
        }
        else if (strncasecmp($method, "findDescBy", 10) === 0) {
            $var = lcfirst(substr($method, 10));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, PicoDatabasePersistent::ORDER_DESC);
        }
        else if (strncasecmp($method, "listBy", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // get sortable
            $sortable = $this->sortableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, $sortable, true);
        }
        else if (strncasecmp($method, "listAscBy", 9) === 0) {
            $var = lcfirst(substr($method, 9));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, PicoDatabasePersistent::ORDER_ASC, true);
        }
        else if (strncasecmp($method, "listDescBy", 10) === 0) {
            $var = lcfirst(substr($method, 10));
            // get pagable
            $pagable = $this->pagableFromParams($params);
            // filter param
            $parameters = $this->valuesFromParams($params);
            return $this->findBy($var, $parameters, $pagable, PicoDatabasePersistent::ORDER_DESC, true);
        }
        else if ($method == "listAllAsc") {
            // get spefification
            $specification = $this->specificationFromParams($params);
            // get pagable
            $pagable = $this->pagableFromParams($params);
            return $this->findAll($specification, $pagable, PicoDatabasePersistent::ORDER_ASC, true);
        }
        else if ($method == "listAllDesc") {
            // get spefification
            $specification = $this->specificationFromParams($params);
            // get pagable
            $pagable = $this->pagableFromParams($params);
            return $this->findAll($specification, $pagable, PicoDatabasePersistent::ORDER_DESC, true);
        }
        else if (strncasecmp($method, "countBy", 6) === 0) {
            $var = lcfirst(substr($method, 6));
            $parameters = $this->valuesFromParams($params);
            return $this->countBy($var, $parameters);
        }
        else if (strncasecmp($method, "existsBy", 8) === 0) {
            $var = lcfirst(substr($method, 8));
            return $this->existsBy($var, $params);
        } else if (strncasecmp($method, "deleteBy", 8) === 0) {
            $var = lcfirst(substr($method, 8));
            return $this->deleteBy($var, $params);
        }
        else if (strncasecmp($method, "booleanToTextBy", 15) === 0) {
            $prop = lcfirst(substr($method, 15));
            return $this->booleanToTextBy($prop, $params);
        }
        else if (strncasecmp($method, "booleanToSelectedBy", 19) === 0) {
            $prop = lcfirst(substr($method, 19));
            return $this->booleanToTextBy($prop, array(' selected="selected"', ''));
        }
        else if (strncasecmp($method, "booleanToCheckedBy", 18) === 0) {
            $prop = lcfirst(substr($method, 18));
            return $this->booleanToTextBy($prop, array(' cheked="checked"', ''));
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
        
        else if (strncasecmp($method, "startsWith", 10) === 0) {
            $var = lcfirst(substr($method, 13));
            $value = $params[0];
            $caseSensitive = isset($params[1]) && $params[1];    
            $haystack = $this->$var;
            return $this->startsWith($haystack, $value, $caseSensitive);
        }  
        else if (strncasecmp($method, "endsWith", 8) === 0) {
            $var = lcfirst(substr($method, 13));
            $value = $params[0];
            $caseSensitive = isset($params[1]) && $params[1];  
            $haystack = $this->$var;
            return $this->endsWith($haystack, $value, $caseSensitive);
        } 
    }
    
    /**
     * Check if string is starts with substring
     *
     * @param string $haystack
     * @param string $value
     * @param bool $caseSensitive
     * @return bool
     */
    private function startsWith($haystack, $value, $caseSensitive = false)
    {
        if($caseSensitive)
        {
            return isset($haystack) && str_starts_with(strtolower($haystack), strtolower($value));
        }
        else
        {
            return isset($haystack) && str_starts_with($haystack, $value);
        }
    }
    
    /**
     * Check if string is ends with substring
     *
     * @param string $haystack
     * @param string $value
     * @param bool $caseSensitive
     * @return bool
     */
    private function endsWith($haystack, $value, $caseSensitive = false)
    {
        if($caseSensitive)
        {
            return isset($haystack) && str_ends_with(strtolower($haystack), strtolower($value));
        }
        else
        {
            return isset($haystack) && str_ends_with($haystack, $value);
        }
    }
    
    /**
     * Get specification from parameters
     * @param array $params
     * @return PicoSpecification|null
     */
    private function specificationFromParams($params)
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
    private function pagableFromParams($params)
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
    private function sortableFromParams($params)
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
    private function valuesFromParams($params)
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
     * Magic method to stringify object
     *
     * @return string
     */
    public function __toString()
    {
        $snake = $this->_snake();
        $pretty = $this->_pretty();
        $flag = $pretty ? JSON_PRETTY_PRINT : 0;
        $obj = clone $this;
        foreach($obj as $key=>$value)
        {
            if($value instanceof self)
            {
                $value = $this->stringifyObject($value, $snake);
                $obj->set($key, $value);
            }
        }
        $upperCamel = $this->isUpperCamel();
        if($upperCamel)
        {         
            $value = $this->valueArrayUpperCamel();
            return json_encode($value, $flag);
        }
        else 
        {
            return json_encode($obj->value($snake), $flag);
        }
    }
    
    /**
     * Stringify object
     *
     * @param self $value
     * @param bool $snake
     * @return mixed
     */
    private function stringifyObject($value, $snake)
    {
        if(is_array($value))
        {
            foreach($value as $key2=>$val2)
            {
                if($val2 instanceof self)
                {
                    $value[$key2] = $val2->stringifyObject($val2, $snake);
                }
            }
        }
        else if(is_object($value))
        {
            foreach($value as $key2=>$val2)
            {
                if($val2 instanceof self)
                {
                    
                    $value->{$key2} = $val2->stringifyObject($val2, $snake);
                }
            }
        }
        return $value->value($snake);
    }
}
