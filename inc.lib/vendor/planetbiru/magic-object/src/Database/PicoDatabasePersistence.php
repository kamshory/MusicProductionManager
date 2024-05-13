<?php
namespace MagicObject\Database;

use DateTime;
use Exception;
use PDO;
use PDOException;
use PDOStatement;
use MagicObject\Exceptions\EmptyResultException;
use MagicObject\Exceptions\EntityException;
use MagicObject\Exceptions\InvalidAnnotationException;
use MagicObject\Exceptions\InvalidFilterException;
use MagicObject\Exceptions\InvalidQueryInputException;
use MagicObject\Exceptions\NoInsertableColumnException;
use MagicObject\Exceptions\NoColumnMatchException;
use MagicObject\Exceptions\NoDatabaseConnectionException;
use MagicObject\Exceptions\NoUpdatableColumnException;
use MagicObject\Exceptions\NoPrimaryKeyDefinedException;
use MagicObject\MagicObject;
use MagicObject\Util\ClassUtil\ExtendedReflectionClass;
use MagicObject\Util\ClassUtil\PicoAnnotationParser;
use MagicObject\Util\ClassUtil\PicoEmptyParameter;
use ReflectionProperty;

class PicoDatabasePersistence // NOSONAR
{
    const ANNOTATION_TABLE = "Table";
    const ANNOTATION_COLUMN = "Column";
    const ANNOTATION_JOIN_COLUMN = "JoinColumn";
    const ANNOTATION_VAR = "var";
    const ANNOTATION_ID = "Id";
    const ANNOTATION_GENERATED_VALUE = "GeneratedValue";
    const ANNOTATION_NOT_NULL = "NotNull";
    const ANNOTATION_DEFAULT_COLUMN = "DefaultColumn";
    const ANNOTATION_JSON_FORMAT = "JsonFormat";
    const SQL_DATE_TIME_FORMAT = "SqlDateTimeFormat";
    
    const KEY_NAME = "name";
    const KEY_REFERENCE_COLUMN_NAME = "referenceColumnName";
    const KEY_NULL = "null";
    const KEY_NOT_NULL = "notnull";
    const KEY_NULLABLE = "nullable";
    const KEY_INSERTABLE = "insertable";
    const KEY_UPDATABLE = "updatable";
    const KEY_STRATEGY = "strategy";
    const KEY_GENERATOR = "generator";
    const KEY_PROPERTY_TYPE = "propertyType";
    const KEY_VALUE = "value";
    const KEY_ENTITY_OBJECT = "entityObject";
    
    const VALUE_TRUE = "true";
    const VALUE_FALSE = "false";

    const ORDER_ASC = "asc";
    const ORDER_DESC = "desc";

    const MESSAGE_NO_RECORD_FOUND = "No record found";
    const MESSAGE_INVALID_FILTER = "Invalid filter";
    const SQL_DATETIME_FORMAT = "Y-m-d H:i:s";
    const DATE_TIME_FORMAT = "datetimeformat";
    
    const NAMESPACE_SEPARATOR = "\\";
    const JOIN_TABLE_SUBFIX = "__jn__";
    const MAX_LINE_LENGTH = 80;

    const COMMA = ", ";
    const COMMA_RETURN = ", \r\n";
    
    /**
     * Database connection
     *
     * @var PicoDatabase
     */
    protected $database;

    /**
     * Object
     *
     * @var mixed
     */
    protected $object;

    /**
     * Class name
     * @var string
     */
    protected $className = "";

    /**
     * Skip null
     *
     * @var boolean
     */
    private $flagIncludeNull = false;

    /**
     * Imported class list
     * @var array
     */
    private $importedClassList = array();

    /**
     * Flag that class list has been processed or not
     * @var boolean
     */
    private $processClassList = false;

    /**
     * Get namespace of class
     * @var string
     */
    private $namespaceName = "";
    
    /**
     * Flag that generated value has been added
     *
     * @var boolean
     */
    private $generatedValue = false;

    /**
     * Flag that entity require database autoincrement
     *
     * @var boolean
     */
    private $requireDbAutoincrement = false;

    /**
     * Flag that database autoincrement has been completed
     *
     * @var boolean
     */
    private $dbAutoinrementCompleted = false;
    
    /**
     * Table Info
     *
     * @var PicoTableInfo
     */
    private $tableInfoProp = null;
    
    /**
     * Entity table cache
     *
     * @var array
     */
    private $entityTable = array();
    
    /**
     * Join map
     *
     * @var PicoJoinMap[]
     */
    protected $joinColumMaps = array();

    /**
     * Flag that WHERE is defined first
     *
     * @var boolean
     */
    protected $whereIsDefinedFirst = false;

    /**
     * WHERE saved on previous
     *
     * @var string
     */
    protected $whereStr = null;


    /**
     * Specification
     *
     * @var PicoSpecification
     */
    protected $specification;

    /**
     * Pageable
     *
     * @var PicoPageable
     */
    protected $pageable;

    /**
     * Sortable
     *
     * @var PicoSortable
     */
    protected $sortable;

    /**
     * Join cache
     *
     * @var array
     */
    private $joinCache = array();

    /**
     * Database connection
     *
     * @param PicoDatabase|null $database
     * @param MagicObject|mixed $object
     */
    public function __construct($database, $object)
    {
        $this->database = $database;
        $this->className = get_class($object);
        $this->object = $object;
    }

    /**
     * Set flag to skip null column
     *
     * @param boolean $skip
     * @return self
     */
    public function includeNull($skip)
    {
        $this->flagIncludeNull = $skip;
        return $this;
    }
    
    /**
     * Check if string is null or empty
     *
     * @param string $string
     * @return string
     */
    private function nulOrEmpty($string)
    {
        return $string == null || empty($string);
    }
    
    /**
     * Check if string is not null and not empty
     *
     * @param string $string
     * @return string
     */
    private function notNullAndNotEmpty($string)
    {
        return $string != null && !empty($string);
    }

    /**
     * Parse key value string
     *
     * @param PicoAnnotationParser $reflexClass
     * @param string $queryString String to be parsed
     * @param string $parameter Parameter name
     * @return array
     */
    private function parseKeyValue($reflexClass, $queryString, $parameter)
    {
        try
        {
            return $reflexClass->parseKeyValue($queryString);
        }
        catch(InvalidQueryInputException $e)
        {
            throw new InvalidAnnotationException("Invalid annotation @".$parameter);
        } 
    }
    
    /**
     * Add column name
     *
     * @param array $columns
     * @param PicoAnnotationParser $reflexProp
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addColumnName($columns, $reflexProp, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_COLUMN) == 0)
            {
                $values = $this->parseKeyValue($reflexProp, $val, $param);
                if(!empty($values))
                {
                    $columns[$prop->name] = $values;
                }
            }
        }
        return $columns;
    }
    
    /**
     * Add column type
     *
     * @param array $columns
     * @param PicoAnnotationParser $reflexProp
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addColumnType($columns, $reflexProp, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_VAR) == 0 && isset($columns[$prop->name]))
            {
                $type = explode(' ', trim($val, " \r\n\t "))[0];
                $columns[$prop->name][self::KEY_PROPERTY_TYPE] = $type;
            }
            if(strcasecmp($param, self::SQL_DATE_TIME_FORMAT) == 0)
            {
                $values = $this->parseKeyValue($reflexProp, $val, $param);
                if(isset($values['pattern']))
                {
                    $columns[$prop->name][self::DATE_TIME_FORMAT] = $values['pattern'];
                }
            }
        }
        return $columns;
    }
    
    /**
     * Add column column name
     *
     * @param array $joinColumns
     * @param PicoAnnotationParser $reflexProp
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addJoinColumnName($joinColumns, $reflexProp, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_JOIN_COLUMN) == 0)
            {
                $values = $this->parseKeyValue($reflexProp, $val, $param);
                if(!empty($values))
                {
                    $joinColumns[$prop->name] = $values;
                }
            }
        }
        return $joinColumns;
    }
    
    /**
     * Add column column type
     *
     * @param array $joinColumns
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addJoinColumnType($joinColumns, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_VAR) == 0 && isset($joinColumns[$prop->name]))
            {
                $type = explode(' ', trim($val, " \r\n\t "))[0];
                $joinColumns[$prop->name][self::KEY_PROPERTY_TYPE] = $type;
                $joinColumns[$prop->name][self::KEY_ENTITY_OBJECT] = true;
            }
        }  
        return $joinColumns;
    }
    
    /**
     * Add primary key
     *
     * @param array $joinColumns
     * @param array $columns
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addPrimaryKey($primaryKeys, $columns, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_ID) == 0 && isset($columns[$prop->name]))
            {
                $primaryKeys[$prop->name] = array(self::KEY_NAME=>$columns[$prop->name][self::KEY_NAME]);
            }
        }
        return $primaryKeys;
    }
    
    /**
     * Add primary key
     *
     * @param array $autoIncrementKeys
     * @param array $columns
     * @param PicoAnnotationParser $reflexClass
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addAutogenerated($autoIncrementKeys, $columns, $reflexClass, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_GENERATED_VALUE) == 0 && isset($columns[$prop->name]))
            {
                $vals = $this->parseKeyValue($reflexClass, $val, $param);
                $autoIncrementKeys[$prop->name] = array(
                    self::KEY_NAME=>isset($columns[$prop->name][self::KEY_NAME])?$columns[$prop->name][self::KEY_NAME]:null,
                    self::KEY_STRATEGY=>isset($vals[self::KEY_STRATEGY])?$vals[self::KEY_STRATEGY]:null,
                    self::KEY_GENERATOR=>isset($vals[self::KEY_GENERATOR])?$vals[self::KEY_GENERATOR]:null
                );
            }
        }
        return $autoIncrementKeys;
    }
    
    /**
     * Add default value
     *
     * @param array $defaultValue
     * @param array $columns
     * @param PicoAnnotationParser $reflexClass
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addDefaultValue($defaultValue, $columns, $reflexClass, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_DEFAULT_COLUMN) == 0)
            {
                $vals = $this->parseKeyValue($reflexClass, $val, $param);
                if(isset($vals[self::KEY_VALUE]))
                {
                    $defaultValue[$prop->name] = array(
                        self::KEY_NAME=>isset($columns[$prop->name][self::KEY_NAME])?$columns[$prop->name][self::KEY_NAME]:null,
                        self::KEY_VALUE=>$vals[self::KEY_VALUE],
                        self::KEY_PROPERTY_TYPE=>$columns[$prop->name][self::KEY_PROPERTY_TYPE]
                    );
                }
            }
        }
        return $defaultValue;
    }
    
    /**
     * Add not null column
     *
     * @param array $notNullColumns
     * @param array $columns
     * @param ReflectionProperty $prop
     * @param array $parameters
     * @return array
     */
    private function addNotNull($notNullColumns, $columns, $prop, $parameters)
    {
        foreach($parameters as $param=>$val)
        {
            if(strcasecmp($param, self::ANNOTATION_NOT_NULL) == 0 && isset($columns[$prop->name]))
            {
                $notNullColumns[$prop->name] = array(self::KEY_NAME=>$columns[$prop->name][self::KEY_NAME]);
            }
        }
        return $notNullColumns;
    }

    /**
     * Get table information by parsing class and property annotation
     *
     * @return PicoTableInfo
     * @throws EntityException
     */
    public function getTableInfo()
    {
        if(!isset($this->tableInfoProp))
        {
            $reflexClass = new PicoAnnotationParser($this->className);
            $table = $reflexClass->getParameter(self::ANNOTATION_TABLE);
            if(!isset($table))
            {
                throw new EntityException($this->className . " is not valid entity");
            }

            $values = $this->parseKeyValue($reflexClass, $table, self::ANNOTATION_TABLE);
    
            $picoTableName = isset($values[self::KEY_NAME]) ? $values[self::KEY_NAME] : "";
            $columns = array();
            $joinColumns = array();
            $primaryKeys = array();
            $autoIncrementKeys = array();
            $notNullColumns = array();
            $props = $reflexClass->getProperties();
            $defaultValue = array();

            // iterate each properties of the class
            foreach($props as $prop)
            {
                $reflexProp = new PicoAnnotationParser($this->className, $prop->name, PicoAnnotationParser::PROPERTY);
                $parameters = $reflexProp->getParameters();

                // get column name of each parameters
                $columns = $this->addColumnName($columns, $reflexProp, $prop, $parameters);
                
                
                // set column type
                $columns = $this->addColumnType($columns, $reflexProp, $prop, $parameters);
                
                
                // get join column name of each parameters
                $joinColumns = $this->addJoinColumnName($joinColumns, $reflexProp, $prop, $parameters);
                
                
                // set join column type
                $joinColumns = $this->addJoinColumnType($joinColumns, $prop, $parameters);
                        

                // list primary key
                $primaryKeys = $this->addPrimaryKey($primaryKeys, $columns, $prop, $parameters);
                

                // list autogenerated column
                $autoIncrementKeys = $this->addAutogenerated($autoIncrementKeys, $columns, $reflexClass, $prop, $parameters);
                
                
                // define default column value
                $defaultValue = $this->addDefaultValue($defaultValue, $columns, $reflexClass, $prop, $parameters);
                

                // list not null column
                $notNullColumns = $this->addNotNull($notNullColumns, $columns, $prop, $parameters);
                
            }
            // bring it together
            $this->tableInfoProp = new PicoTableInfo($picoTableName, $columns, $joinColumns, $primaryKeys, $autoIncrementKeys, $defaultValue, $notNullColumns);
        }
        return $this->tableInfoProp;
    }

    /**
     * Get match row
     *
     * @param PDOStatement $stmt
     * @return boolean
     */
    public function matchRow($stmt)
    {
        if($stmt == null)
        {
            return false;
        }
        $rowCount = $stmt->rowCount();
        return $rowCount != null && $rowCount > 0;
    }
    
    /**
     * Save data to database
     *
     * @param boolean $includeNull
     * @return PDOStatement|EntityException
     */
    public function save($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $stmt = null;
        try
        {
            $where = $this->getWhere($info, $queryBuilder);
            if(!$this->isValidFilter($where))
            {
                throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
            }
            $data2saved = clone $this->object->value();
            $data = $this->_select($info, $queryBuilder, $where);
            if($data != null)
            {
                // save current data
                foreach($data2saved as $prop=>$value)
                {
                    if($value != null)
                    {
                        $this->object->set($prop, $value);
                    }
                }
                $stmt = $this->_update($info, $queryBuilder, $where);
            }
            else
            {
                $stmt = $this->_insert($info, $queryBuilder);
            }
        }
        catch(Exception $e)
        {
            $stmt = $this->_insert($info, $queryBuilder);
        }     
        return $stmt;
    }

    /**
     * Query of save data
     *
     * @param boolean $includeNull
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException
     */
    public function saveQuery($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $query = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        try
        {
            $where = $this->getWhere($info, $queryBuilder);
            if(!$this->isValidFilter($where))
            {
                throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
            }
            $data2saved = clone $this->object->value();
            $data = $this->_select($info, $queryBuilder, $where);
            if($data != null)
            {
                // save current data
                foreach($data2saved as $prop=>$value)
                {
                    if($value != null)
                    {
                        $this->object->set($prop, $value);
                    }
                }
                $query = $this->_updateQuery($info, $queryBuilder, $where);
            }
            else
            {
                $query = $this->_insertQuery($info, $queryBuilder);
            }
        }
        catch(Exception $e)
        {
            $query = $this->_insertQuery($info, $queryBuilder);
        }     
        return $query;
    }


    /**
     * Get object values
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return array
     */
    private function getValues($info, $queryBuilder)
    {
        $values = array();
        foreach($info->getColumns() as $property=>$column)
        {
            $columnName = $column[self::KEY_NAME];
            $value = $this->object->get($property);
            $value = $this->fixInput($value, $column);
            if($this->flagIncludeNull || $value !== null)
            {
                $value = $queryBuilder->escapeValue($value);
                $values[$columnName] = $value;
            }
        }
        return $values;
    }

    /**
     * Get null column set manualy
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function getNullCols($info)
    {
        $nullCols = array();
        $nullList = $this->object->nullPropertiyList();
        if(isset($nullList) && is_array($nullList))
        {
            foreach($nullList as $key=>$val)
            {
                if($val === true && isset($info->getColumns()[$key]))
                {
                    $columnName = $info->getColumns()[$key][self::KEY_NAME];
                    $nullCols[] = $columnName;
                }
            }
        }
        return $nullCols;
    }
    
    /**
     * Get noninsertable column
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function getNonInsertableCols($info)
    {
        $nonInsertableCols = array();
        foreach($info->getColumns() as $params)
        {
            if(isset($params) 
                && isset($params[self::KEY_INSERTABLE])
                && strcasecmp($params[self::KEY_INSERTABLE], self::VALUE_FALSE) == 0              
                )
            {
                $columnName = $params[self::KEY_NAME];
                $nonInsertableCols[] = $columnName;
            }
        }
        return $nonInsertableCols;
    }
    
    /**
     * Get nonupdatable column
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function getNonUpdatableCols($info)
    {
        $nonUpdatableCols = array();
        foreach($info->getColumns() as $params)
        {
            if(isset($params) 
                && isset($params[self::KEY_UPDATABLE])
                && strcasecmp($params[self::KEY_UPDATABLE], self::VALUE_FALSE) == 0              
                )
            {
                $columnName = $params[self::KEY_NAME];
                $nonUpdatableCols[] = $columnName;
            }
        }
        return $nonUpdatableCols;
    }

    /**
     * Get SET statement
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return string
     */
    private function getSet($info, $queryBuilder)
    {
        $sets = array();
        $primaryKeys = $this->getPrimaryKeys($info);
        $nullCols = $this->getNullCols($info);
        $nonUpdatableCols = $this->getNonUpdatableCols($info);
        foreach($info->getColumns() as $property=>$column)
        {
            $columnName = $column[self::KEY_NAME];
            if(!$this->isPrimaryKeys($columnName, $primaryKeys))
            {
                $value = $this->object->get($property);
                $value = $this->fixInput($value, $column);
                if(($this->flagIncludeNull || $value !== null) 
                    && !in_array($columnName, $nullCols) 
                    && !in_array($columnName, $nonUpdatableCols)
                    )
                {
                    $value = $queryBuilder->escapeValue($value);
                    $sets[] = $columnName . " = " . $value;
                }
            }
        }
        foreach($nullCols as $columnName)
        {
            $sets[] = "$columnName = null";
        }
        if(empty($sets))
        {
            throw new NoUpdatableColumnException("No updatable column");
        }
        return $this->joinStringArray($sets, self::MAX_LINE_LENGTH, self::COMMA, self::COMMA_RETURN);
    }

    /**
     * Get WHERE statement
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return string
     */
    private function getWhere($info, $queryBuilder)
    {
        if($this->whereIsDefinedFirst && !empty($this->whereStr))
        {
            return $this->whereStr;
        }
        $wheres = array();
        foreach($info->getPrimaryKeys() as $property=>$column)
        {
            $columnName = $column[self::KEY_NAME];
            $value = $this->object->get($property);
            $value = $queryBuilder->escapeValue($value);
            if(strcasecmp($value, self::KEY_NULL) == 0)
            {
                $wheres[] = $columnName . " is null";
            }
            else
            {
                $wheres[] = $columnName . " = " . $value;
            }
        }
        if(empty($wheres))
        {
            throw new NoPrimaryKeyDefinedException("No primary key defined");
        }
        return implode(" and ", $wheres);
    }

    /**
     * Get primary keys
     *
     * @param PicoTableInfo $info Table information
     * @return array
     * @throws EntityException
     */
    public function getPrimaryKeys($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $primaryKeys = array();
        foreach($info->getPrimaryKeys() as $column)
        {
            $primaryKeys[] = $column[self::KEY_NAME];
        }
        return $primaryKeys;
    }

    /**
     * Get columns
     *
     * @param PicoTableInfo $info Table information
     * @return array
     * @throws EntityException
     */
    public function getColumns($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $columns = array();
        foreach($info->getColumns() as $column)
        {
            $columns[] = $column[self::KEY_NAME];
        }
        return $columns;
    }

    /**
     * Get join columns
     *
     * @param PicoTableInfo $info Table information
     * @return array
     * @throws EntityException
     */
    public function getJoinSources($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $joinColumns = array();
        foreach($info->getJoinColumns() as $joinColumn)
        {
            $joinColumns[] = $joinColumn[self::KEY_NAME];
        }
        return $joinColumns;
    }

    /**
     * Check if column is primary key or not
     *
     * @param string $columnName
     * @param array $primaryKeys
     * @return boolean
     */
    public function isPrimaryKeys($columnName, $primaryKeys)
    {
        return in_array($columnName, $primaryKeys);
    }

    /**
     * Get primary key with autoincrement value
     *
     * @param PicoTableInfo $info Table information
     * @return array
     */
    public function getPrimaryKeyAutoIncrement($info)
    {
        $aiKeys = array();
        if($info->getAutoIncrementKeys() != null)
        {
            $primaryKeys = array_keys($info->getPrimaryKeys());
            foreach($info->getAutoIncrementKeys() as $name=>$value)
            {
                if(in_array($name, $primaryKeys))
                {
                    $aiKeys[$name] = $value;
                }
            }
        }
        return $aiKeys;
    }
    
    /**
     * Add generated value
     *
     * @param PicoTableInfo $info Table information
     * @param boolean $fisrtCall
     * @return void
     */
    private function addGeneratedValue($info, $fisrtCall)
    {
        if(!$this->generatedValue)
        {
            $keys = $info->getAutoIncrementKeys();
            if(isset($keys) && is_array($keys))
            {
                foreach($keys as $prop=>$col)
                {
                    $autoVal = $this->object->get($prop);
                    if($this->nulOrEmpty($autoVal) && isset($col[self::KEY_STRATEGY]))
                    {
                        $this->setGeneratedValue($prop, $col[self::KEY_STRATEGY], $fisrtCall);
                    }
                }
            }
        }
    }
    
    /**
     * Set generated value
     *
     * @param string $prop
     * @param string $strategy
     * @return void
     */
    private function setGeneratedValue($prop, $strategy, $fisrtCall)
    {
        if(strcasecmp($strategy, "GenerationType.UUID") == 0)
        {
            $generatedValue = $this->database->generateNewId();
            $this->object->set($prop, $generatedValue);
            if($fisrtCall)
            {
                $this->generatedValue = true;
            }
        }
        if(strcasecmp($strategy, "GenerationType.IDENTITY") == 0)
        {
            if($fisrtCall)
            {
                $this->requireDbAutoincrement = true;
            }
            else if($this->requireDbAutoincrement && !$this->dbAutoinrementCompleted)
            {
                $generatedValue = $this->database->getDatabaseConnection()->lastInsertId();
                $this->object->set($prop, $generatedValue);
                $this->dbAutoinrementCompleted = true;
            }         
        }
    }

    /**
     * Insert data
     *
     * @param boolean $includeNull
     * @return PDOStatement|EntityException
     */
    public function insert($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        return $this->_insert($info, $queryBuilder);
    }

    /**
     * Query of insert data
     *
     * @param boolean $includeNull
     * @return PicoDatabaseQueryBuilder|EntityException
     */
    public function insertQuery($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        return $this->_insertQuery($info, $queryBuilder);
    }

    /**
     * Insert data
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return PDOStatement
     */
    private function _insert($info = null, $queryBuilder = null)
    {
        $sqlQuery = $this->_insertQuery($info, $queryBuilder);
        $stmt = $this->database->executeInsert($sqlQuery);
        if(!$this->generatedValue)
        {
            $this->addGeneratedValue($info, false);
            $this->object->update();
        }
        return $stmt;
    }

    /**
     * Insert data
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException
     */
    private function _insertQuery($info = null, $queryBuilder = null)
    {
        $this->generatedValue = false;
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $this->addGeneratedValue($info, true);
        $values = $this->getValues($info, $queryBuilder);
        $fixValues = $this->fixInsertableValues($values, $info);       
        
        return $queryBuilder
            ->newQuery()
            ->insert()
            ->into($info->getTableName())
            ->fields($this->createStatementFields($fixValues))
            ->values($this->createStatementValues($fixValues));
    }
    
    /**
     * Fix insertable values
     *
     * @param array $values
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function fixInsertableValues($values, $info = null)
    {
        $fixedValues = array();
        if($info != null)
        {
            $insertableCols = array();
            $nonInsertableCols = $this->getNonInsertableCols($info);
            foreach($values as $key=>$value)
            {
                if(!in_array($key, $nonInsertableCols))
                {
                    $insertableCols[$key] = $value;
                }
            }
            $fixedValues = $insertableCols;        
        }
        else
        {
            $fixedValues = $values;
        }       
        
        /**
         * 1. TABLE - Indicates that the persistence provider must assign primary keys for the entity using an underlying database table to ensure uniqueness.
         * 2. SEQUENCE - Indicates that the persistence provider must assign primary keys for the entity using a database sequence.
         * 3. IDENTITY - Indicates that the persistence provider must assign primary keys for the entity using a database identity column.
         * 4. AUTO - Indicates that the persistence provider should pick an appropriate strategy for the particular database. The AUTO generation strategy may expect a database resource to exist, or it may attempt to create one. A vendor may provide documentation on how to create such resources in the event that it does not support schema generation or cannot create the schema resource at runtime.
         * 5. UUID - Indicates that the persistence provider must assign primary keys for the entity with a UUID value.
         */ 
        
        if($info->getAutoIncrementKeys() != null)
        {
            foreach($info->getAutoIncrementKeys() as $name=>$col)
            {
                if(strcasecmp($col[self::KEY_STRATEGY], "GenerationType.UUID") == 0)
                {
                    $value = $this->database->generateNewId();
                    $values[$col[self::KEY_NAME]] = $value;
                    $this->object->set($name, $value);
                }
            }
        }        
        if(empty($fixedValues))
        {
            throw new NoInsertableColumnException("No insertable column");
        }
        return $fixedValues;
    }

    /**
     * Implode array keys to field list
     *
     * @param array $values
     * @param PicoTableInfo $info Table information
     * @return string
     */
    public function createStatementFields($values)
    {
        return "(".implode(self::COMMA, array_keys($values)).")";
    }

    /**
     * Implode array values to value list
     *
     * @param array $values
     * @return string
     */
    public function createStatementValues($values)
    {      
        return "(".implode(self::COMMA, array_values($values)).")";
    }

    /**
     * Get table column name from an object property
     *
     * @param string $propertyNames
     * @param array $columns
     * @return string
     */
    private function getColumnNames($propertyNames, $columns)
    {
        $source = str_replace("And", "#And#", $propertyNames."#");
        $source = str_replace("Or", "#Or#", $source);

        $source = str_replace("#Or#Or", "Or#Or", $source);
        $source = str_replace("#And#And", "And#And", $source);

        $sourcex = str_replace("#And#", "@", $source);
        $sourcex = str_replace("#Or#", "@", $sourcex);
        
        $sourcex = trim($sourcex, "#");
        
        $arrSource = explode("@", $sourcex);
        foreach($arrSource as $idx=>$val)
        {
            $arrSource[$idx] = lcfirst($val);
        }

        $result = $propertyNames;

        foreach($arrSource as $pro)
        {
            if (isset($columns[$pro]))
            {
                $col = $columns[$pro];
                $columnName = $col[self::KEY_NAME];
                $result = str_ireplace($pro, " ".$columnName." ? ", $result);
            }
        }
        if($result != $propertyNames)
        {
            return $result;
        }
        throw new NoColumnMatchException("No column match");
    }

    /**
     * Ger column map
     *
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function getColumnMap($info)
    {
        $maps = array();
        if($info->getJoinColumns() != null)
        {
            foreach($info->getJoinColumns() as $key=>$value)
            {
                $maps[$key] = $value[self::KEY_NAME];
            }
        }
        if($info->getColumns() != null)
        {
            foreach($info->getColumns() as $key=>$value)
            {
                $maps[$key] = $value[self::KEY_NAME];
            }
        }
        return $maps;
    }

    /**
     * Fix comparison
     *
     * @param string $column
     * @return string
     */
    private function fixComparison($column)
    {
        if(stripos($column, ' ') !== false)
        {
            $arr = explode(' ', $column);
            foreach($arr as $idx=>$val)
            {
                if($val == 'And')
                {
                    $arr[$idx] = 'and';
                }
                if($val == 'Or')
                {
                    $arr[$idx] = 'or';
                }
            }
            $column = implode(' ', $arr);
        }
        return $column;
    }

    /**
     * Create WHERE by argument given
     *
     * @param object $info
     * @param string $propertyName
     * @param array $propertyValues
     * @return string
     */
    private function createWhereFromArgs($info, $propertyName, $propertyValues)
    {
        $columnNames = $this->getColumnNames($propertyName, $info->getColumns());
        $arr = explode("?", $columnNames);
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $wheres = array();
        for($i = 0; $i < count($arr) - 1 && $i < count($propertyValues); $i++)
        {
            $column = ltrim($this->fixComparison($arr[$i]), ' ');
            if($propertyValues[$i] instanceof PicoDataComparation)
            {
                $wheres[] = $column . $propertyValues[$i]->getComparison() . " ". $queryBuilder->escapeValue($propertyValues[$i]->getValue());
            }
            else
            {
                $value = $queryBuilder->escapeValue($propertyValues[$i]);
                if(strcasecmp($value, 'null') == 0)
                {
                    $wheres[] = $column . "is " . $value;
                }
                else
                {
                    $wheres[] = $column . "= " . $value;
                }
            }
        }
        return $this->joinStringArray($wheres, self::MAX_LINE_LENGTH);
    }
    
    /**
     * Get table name
     *
     * @param string|null $entityName
     * @return string|null
     */
    private function getTableOf($entityName)
    {
        if($entityName == null || empty($entityName))
        {
            return null;
        }
        if(isset($this->entityTable[$entityName]))
        {
            return $this->entityTable[$entityName];
        }
        $tableName = $entityName;
        try
        {
            $className = $this->getRealClassName($entityName);
            $annotationParser = new PicoAnnotationParser($className);
            $parameters = $annotationParser->getParametersAsObject();
            if($parameters->getTable() != null)
            {
                $attribute = $annotationParser->parseKeyValueAsObject($parameters->getTable());
                if($attribute->getName() != null)
                {
                    $tableName = $attribute->getName();
                    $this->entityTable[$entityName] = $tableName;
                    
                    
                    //$this->joinPrimaryKeys[]
                }
            }
        }
        catch(Exception $e)
        {
            // do nothing
            $tableName = null;
        }
        
        return $tableName;
    }
    
    private function getPrimaryKeyOf($entityName)
    {
        $columns = array();
        try
        {
            $className = $this->getRealClassName($entityName);
            $annotationParser = new PicoAnnotationParser($className);
            $props = $annotationParser->getProperties();
            foreach($props as $prop)
            {
                $reflexProp = new PicoAnnotationParser($className, $prop->name, PicoAnnotationParser::PROPERTY);
                $parameters = $reflexProp->getParametersAsObject();
                if($parameters->getId() != null && $parameters->getId() instanceof PicoEmptyParameter)
                {
                    $properties = $reflexProp->parseKeyValueAsObject($parameters->getColumn());
                    $columns[$prop->name] = $properties->getName();
                }

                // get column name of each parameters
                
            }
        }
        catch(Exception $e)
        {
            // do nothing
        }
        return $columns;
    }
    
    /**
     * Get column maps of the entity
     *
     * @param string $entityName
     * @return array
     */
    private function getColumnMapOf($entityName)
    {
        $columns = array();
        try
        {
            $className = $this->getRealClassName($entityName);
            $annotationParser = new PicoAnnotationParser($className);
            $props = $annotationParser->getProperties();
            foreach($props as $prop)
            {
                $reflexProp = new PicoAnnotationParser($className, $prop->name, PicoAnnotationParser::PROPERTY);
                $parameters = $reflexProp->getParametersAsObject();
                $properties = $reflexProp->parseKeyValueAsObject($parameters->getColumn());
                $columns[$prop->name] = $properties->getName();

                // get column name of each parameters
                
            }
        }
        catch(Exception $e)
        {
            // do nothing
        }
        return $columns;
    }
    
    /**
     * Get join source
     *
     * @param string|null $parentName
     * @param string $masterTable
     * @param string|null $entityTable
     * @param string $field
     * @return string
     */
    private function getJoinSource($parentName, $masterTable, $entityTable, $field, $master = false)
    {
 
        $result = $masterTable.".".$field;
        if($entityTable != null && $parentName != null)
        {
            if($master)
            {
                $result = $entityTable.".".$field;
            }
            else if(isset($this->joinColumMaps[$parentName]))
            {
                $joinMap = $this->joinColumMaps[$parentName];
                $aliasTable = $joinMap->getJoinTableAlias();
                $result = $aliasTable.".".$field;
            }
        }
        return $result;
    } 

    /**
     * Create WHERE from specification
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoSpecification $specification
     * @param PicoTableInfo $info Table information
     * @return string
     */
    protected function createWhereFromSpecification($sqlQuery, $specification, $info)
    {
        $masterColumnMaps = $this->getColumnMap($info);
        
        $arr = array();
        $arr[] = "(1=1)";
        if($specification != null && !$specification->isEmpty())
        {
            $specifications = $specification->getSpecifications();
            foreach($specifications as $spec)
            {           
                $arr = $this->addWhere($arr, $masterColumnMaps, $sqlQuery, $spec, $info);
            }
        }
        $ret = $this->joinStringArray($arr, self::MAX_LINE_LENGTH);
        return $this->trimWhere($ret);
    }

    /**
     * Join array string with maximum length. If max is reached, it will create new line
     *
     * @param string[] $arr
     * @param integer $max
     * @param string $normalSplit
     * @param string $maxSplit
     * @return string
     */
    private function joinStringArray($arr, $max = 0, $normalSplit = " ", $maxSplit = " \r\n")
    {
        if($arr == null)
        {
            return "";
        }
        if($max == 0)
        {
            return implode($normalSplit, $arr);
        }
        $arr2 = array();
        $idx = 0;
        foreach($arr as $value)
        {
            if(!isset($arr2[$idx]))
            {
                $arr2[$idx] = array();
            }
            if(strlen(implode($normalSplit, $arr2[$idx])) + strlen($value) < $max)
            {
                $arr2[$idx][] = $value;
            }
            else
            {
                $idx++;
                $arr2[$idx][] = $value;
            }         
        }
        $arr3 = array();
        foreach($arr2 as $value)
        {
            $arr3[] = implode($normalSplit, $value);
        }
        return implode($maxSplit, $arr3);
    }
    
    /**
     * Add where statemenet
     *
     * @param array $arr
     * @param array $masterColumnMaps
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoSpecification $spec
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function addWhere($arr, $masterColumnMaps, $sqlQuery, $spec, $info)
    {
        if($spec instanceof PicoPredicate)
        {
            $masterTable = $info->getTableName();
            $entityField = new PicoEntityField($spec->getField());
            $field = $entityField->getField();
            $entityName = $entityField->getEntity();
            $parentName = $entityField->getParentField();
            
            if($entityName != null)
            {
                $entityTable = $this->getTableOf($entityName);
                
                if($entityTable != null)
                {
                    $joinColumnmaps = $this->getColumnMapOf($entityName);                           
                    $maps = $joinColumnmaps;
                }
                else
                {
                    $maps = $masterColumnMaps;
                }
            }
            else
            {
                $entityTable = null;
                $maps = $masterColumnMaps;
            }
            
            $columnNames = array_values($maps);          
            // flat
            if(isset($maps[$field]))
            {
                // get from map
                $column = $this->getJoinSource($parentName, $masterTable, $entityTable, $maps[$field], $entityTable == $masterTable);
                $arr[] = $spec->getFilterLogic() . " " . $column . " " . $spec->getComparation()->getComparison() . " " . $sqlQuery->escapeValue($spec->getValue());
            }
            else if(in_array($field, $columnNames))
            {
                // get colum name
                $column = $this->getJoinSource($parentName, $masterTable, $entityTable, $field, $entityTable == $masterTable);
                $arr[] = $spec->getFilterLogic() . " " . $column . " " . $spec->getComparation()->getComparison() . " " . $sqlQuery->escapeValue($spec->getValue());
            }
        }
        else if($spec instanceof PicoSpecification)
        {
            // nested
            $arr[] = $spec->getParentFilterLogic() . " (" . $this->createWhereFromSpecification($sqlQuery, $spec, $info) . ")";
        }
        return $arr;
    }
    
    /**
     * Trim WHERE
     *
     * @param string $where
     * @return string
     */
    private function trimWhere($where)
    {
        if(stripos($where, "(1=1) or ") === 0)
        {
            $where = substr($where, 9);
        }
        if(stripos($where, "(1=1) and ") === 0)
        {
            $where = substr($where, 10);
        }
        return $where;
    }

    /**
     * Create ORDER BY
     *
     * @param object $info
     * @param PicoSortable|string $order
     * @return string|null
     */
    private function createOrderBy($info, $order)
    {
        if($order instanceof PicoSortable)
        {
            return $this->createOrderByQuery($order, $info);
        }
        else if(is_string($order))
        {
            $orderBys = array();
            $pKeys = array_values($info->getPrimaryKeys());
            if(!empty($pKeys))
            {
                foreach($pKeys as $pKey)
                {
                    $pKeyCol = $pKey[self::KEY_NAME];
                    $orderBys[] = $pKeyCol." ".strtolower($order);
                }
            }
            return $this->joinStringArray($orderBys, self::MAX_LINE_LENGTH, self::COMMA, self::COMMA_RETURN);
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Create sort by
     *
     * @param PicoSortable $order
     * @param PicoTableInfo $info Table information
     * @return string
     */
    public function createOrderByQuery($order, $info = null)
    {
        if($order->getSortable() == null || !is_array($order->getSortable()) || empty($order->getSortable()))
        {
            return null;
        }
        $ret = null;
        if($info == null)
        {
            $ret = $this->createWithoutMapping($order);
        }
        else
        {
            $ret = $this->createWithMapping($order, $info);
        }
        return $ret;
    }
    
    /**
     * Create sort without mapping
     *
     * @param PicoSortable $order
     * @return string
     */
    private function createWithoutMapping($order)
    {
        $ret = null;
        $sorts = array();
        foreach($order->getSortable() as $sortable)
        {
            $columnName = $sortable->getSortBy();
            $sortType = $sortable->getSortType();             
            $sortBy = $columnName;
            $entityField = new PicoEntityField($sortBy);
            if($entityField->getEntity() != null)
            {
                $tableName = $this->getTableOf($entityField->getEntity());
                $sortBy = $tableName.".".$sortBy;
            }
            $sorts[] = $sortBy . " " . $sortType;           
        }
        if(!empty($sorts))
        {
            $ret = $this->joinStringArray($sorts, self::MAX_LINE_LENGTH, self::COMMA, self::COMMA_RETURN);
        }
        return $ret;
    }
    
    /**
     * Create sort with mapping
     *
     * @param PicoSortable $order
     * @param PicoTableInfo $info Table information
     * @return string
     */
    private function createWithMapping($order, $info)
    {
        $masterColumnMaps = $this->getColumnMap($info);
        $masterTable = $info->getTableName();
        
        $arr = array();
    
        foreach($order->getSortable() as $sortOrder)
        {           
            $entityField = new PicoEntityField($sortOrder->getSortBy());
            $field = $entityField->getField();
            $entityName = $entityField->getEntity();
            $parentName = $entityField->getParentField();
            if($entityName != null)
            {
                $entityTable = $this->getTableOf($entityName);
                if($entityTable != null)
                {
                    $joinColumnmaps = $this->getColumnMapOf($entityName);                           
                    $maps = $joinColumnmaps;
                }
                else
                {
                    $maps = $masterColumnMaps;
                }
                $columnNames = array_values($maps);
            }
            else
            {
                $entityTable = null;
                $maps = $masterColumnMaps;
                $columnNames = array_values($maps);
            }
            
            if(isset($maps[$field]))
            {
                // get from map
                $column = $this->getJoinSource($parentName, $masterTable, $entityTable, $maps[$field], $masterTable == $entityTable);
                
                $arr[] = $column . " " . $sortOrder->getSortType();
            }
            else if(in_array($field, $columnNames))
            {
                // get colum name
                $column = $this->getJoinSource($parentName, $masterTable, $entityTable, $field, $masterTable == $entityTable);
                $arr[] = $column . " " . $sortOrder->getSortType();
            }
        }
        return $this->joinStringArray($arr, self::MAX_LINE_LENGTH, self::COMMA, self::COMMA_RETURN);
    }
    
    /**
     * Check if primary key has valid value or not
     *
     * @param string[] $primaryKeys
     * @param array $propertyValues
     * @return boolean
     */
    private function isValidPrimaryKeyValues($primaryKeys, $propertyValues)
    {
        return isset($primaryKeys) && !empty($primaryKeys) && count($primaryKeys) <= count($propertyValues);
    }
    
    /**
     * Find one record by primary key value
     *
     * @param array $propertyValues
     * @return object
     * @throws EntityException|InvalidFilterException|EmptyResultException
     */
    public function find($propertyValues)
    {
        $data = null;
        $info = $this->getTableInfo();
        
        $primaryKeys = $info->getPrimaryKeys();
        
        if($this->isValidPrimaryKeyValues($primaryKeys, $propertyValues))
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
            $wheres = array();
            $index = 0;
            foreach($primaryKeys as $primatyKey)
            {
                $columnName = $primatyKey[self::KEY_NAME];
                $columnValue = $propertyValues[$index];
                if($columnValue === null)
                {
                    $wheres[] = $columnName . " is null";
                }
                else
                {
                    $wheres[] = $columnName . " = " . $queryBuilder->escapeValue($propertyValues[$index]);
                }
            }
            $where = implode(" and ", $wheres);
            if(!$this->isValidFilter($where))
            {
                throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
            }
            $sqlQuery = $queryBuilder
                ->newQuery()
                ->select($info->getTableName().".*")
                ->from($info->getTableName())
                ->where($where)
                ->limit(1)
                ->offset(0);
            try
            {
                $stmt = $this->database->executeQuery($sqlQuery);
                if($this->matchRow($stmt))
                {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $data = $this->fixDataType($row, $info); 
                    $data = $this->join($data, $row, $info);
                    return $data;
                }
                else
                {
                    throw new EmptyResultException(self::MESSAGE_NO_RECORD_FOUND);
                }
            }
            catch(Exception $e)
            {
                throw new EmptyResultException($e->getMessage());
            }
        }
        else
        {
            throw new EmptyResultException("No primary key set");
        }
    }

    /**
     * Add specification to query builder
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoSpecification|array $specification
     * @param PicoTableInfo $info Table information
     * @return PicoDatabaseQueryBuilder
     */
    private function setSpecification($sqlQuery, $specification, $info)
    {
        if($specification != null && $specification instanceof PicoSpecification && !$specification->isEmpty())
        {
            $where = $this->createWhereFromSpecification($sqlQuery, $specification, $info);
            if($this->notNullAndNotEmpty($where))
            {
                $sqlQuery->where($where);
            }
        }
        return $sqlQuery;
    }

    /**
     * Add pagable to query builder
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoPageable $pageable
     * @return PicoDatabaseQueryBuilder
     */
    private function setPagable($sqlQuery, $pageable)
    {
        if($pageable instanceof PicoPageable)
        {
            $offsetLimit = $pageable->getOffsetLimit();
            if($offsetLimit != null)
            {
                $limit = $offsetLimit->getLimit();
                $offset = $offsetLimit->getOffset();
                $sqlQuery->limit($limit);
                $sqlQuery->offset($offset);
            }
        }
        return $sqlQuery;
    }
        
    /**
     * Add sortable to query builder
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $pageable
     * @param PicoTableInfo $info Table information
     * @return PicoDatabaseQueryBuilder
     */
    private function setSortable($sqlQuery, $pageable, $sortable, $info)
    {
        if($sortable != null)
        {
            if($sortable instanceof PicoSortable)
            {
                $sortOrder = $this->createOrderByQuery($sortable, $info);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
            else if(is_string($sortable))
            {
                $sortOrder = $this->createOrderBy($info, $sortable);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
        } 
        else if($pageable != null && $pageable instanceof PicoPageable)
        {
            $sortOrder = $pageable->createOrderBy($info);
            if($this->notNullAndNotEmpty($sortOrder))
            {
                $sqlQuery->orderBy($sortOrder);
            }
            else if(is_string($sortable))
            {
                $sortOrder = $this->createOrderBy($info, $sortable);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
            $offsetLimit = $pageable->getOffsetLimit();
            if($offsetLimit != null)
            {
                $limit = $offsetLimit->getLimit();
                $offset = $offsetLimit->getOffset();
                $sqlQuery->limit($limit);
                $sqlQuery->offset($offset);
            }
        }
        else if(is_string($pageable))
        {
            $sortOrder = $this->createOrderBy($info, $pageable);
            $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
        }
        return $sqlQuery;
    }

    /**
     * Set ORDER BY
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param string $sortOrder
     * @return PicoDatabaseQueryBuilder
     */
    private function setOrdeBy($sqlQuery, $sortOrder)
    {
        if($this->notNullAndNotEmpty($sortOrder))
        {
            $sqlQuery->orderBy($sortOrder);
        }
        return $sqlQuery;
    }
    
    /**
     * Add JOIN query
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoTableInfo $info Table information
     * @return PicoDatabaseQueryBuilder
     */
    protected function addJoinQuery($sqlQuery, $info)
    {
        $joinColumns = $info->getJoinColumns();
        
        $masterTable = $info->getTableName();
        $tableAlias = array();
        
        foreach($joinColumns as $propertyName=>$joinColumn)
        {
            $entity = $joinColumn[self::KEY_PROPERTY_TYPE];
            $columnName = $joinColumn[self::KEY_NAME];
            $joinTable = $this->getTableOf($entity);
            if(!isset($tableAlias[$joinTable]))
            {
                $tableAlias[$joinTable] = 0;
            }
            $tableAlias[$joinTable]++;
            
            $joinTableAlias = $joinTable.self::JOIN_TABLE_SUBFIX.$tableAlias[$joinTable];
            
            $this->joinColumMaps[$propertyName] = new PicoJoinMap($propertyName, $columnName, $entity, $joinTable, $joinTableAlias);
            
            $joinColumn = $this->getPrimaryKeyOf($entity);

            $joinPrimaryKeys = array_values($this->getPrimaryKeyOf($entity));

            if(isset($joinColumn[self::KEY_REFERENCE_COLUMN_NAME]))
            {
                $referenceColumName = $joinColumn[self::KEY_REFERENCE_COLUMN_NAME];
            }
            else if(!empty($joinPrimaryKeys))
            {
                $referenceColumName = $joinPrimaryKeys[0];
            }
            else
            {
                $referenceColumName = $joinColumn[self::KEY_NAME];
            }
            
            $sqlQuery->leftJoin($joinTable." ".$joinTableAlias)->on($joinTableAlias.".".$referenceColumName." = ".$masterTable.".".$columnName);
        }
        return $sqlQuery;
    }
    
    /**
     * Check if need join query
     *
     * @param PicoSpecification $specification
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $sortable
     * @param PicoTableInfo $info Table information
     * @return boolean
     */
    protected function isRequireJoin($specification, $pageable, $sortable, $info)
    {
        if($specification->isRequireJoin())
        {
            return true;
        }
        $result = false;
        if($sortable != null)
        {
            if($sortable instanceof PicoSortable)
            {
                $sr = $sortable->getSortable();
                foreach($sr as $s)
                {
                    if(strpos($s->getSortBy(), ".") !== false)
                    {
                        $result = true;
                        break;
                    }
                }
            }
            else if(is_string($sortable))
            {
                $result = strpos($sortable, ".") !== false;
            }
        } 
        else if($pageable != null && $pageable instanceof PicoPageable)
        {
            $result = strpos($pageable->createOrderBy($info), ".") !== false;
        }
        else if(is_string($pageable))
        {
            $result = strpos($this->createOrderBy($info, $pageable), ".") !== false;
        }
        return $result;
    }

    /**
     * Get findAll query
     *
     * @param PicoSpecification $specification
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $sortable
     * @param PicoTableInfo $info Table information
     * @return PicoDatabaseQueryBuilder
     */
    public function findAllQuery($specification, $pageable = null, $sortable = null, $info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $selected = $info->getTableName().".*";
        return $this->findSpecificQuery($selected, $specification, $pageable, $sortable, $info);
    }
    
    /**
     * Get findSpecific query
     *
     * @param string $selected
     * @param PicoSpecification $specification
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $sortable
     * @param PicoTableInfo $info Table information
     * @return PicoDatabaseQueryBuilder
     */
    public function findSpecificQuery($selected, $specification, $pageable = null, $sortable = null, $info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($selected)
            ->from($info->getTableName());
        
        if($this->isRequireJoin($specification, $pageable, $sortable, $info))
        {
            $sqlQuery = $this->addJoinQuery($sqlQuery, $info);
        }
            
        if($specification != null)
        {
            $sqlQuery = $this->setSpecification($sqlQuery, $specification, $info);
        }
        
        if($pageable != null)
        {
            $sqlQuery = $this->setPagable($sqlQuery, $pageable);      
        }
        
        if($pageable != null || $sortable != null)
        {
            $sqlQuery = $this->setSortable($sqlQuery, $pageable, $sortable, $info);        
        }
        return $queryBuilder;
    }

    /**
     * Get all record from database wihout filter
     *
     * @param PicoSpecification $specification
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $sortable
     * @return array|null
     * @throws EntityException|EmptyResultException
     */
    public function findAll($specification, $pageable = null, $sortable = null)
    {
        $info = $this->getTableInfo();     
        $selected = $info->getTableName().".*";
        return $this->findSpecific($selected, $specification, $pageable, $sortable);
    }

    /**
     * Get all record from database wihout filter
     *
     * @param string $selected
     * @param PicoSpecification $specification
     * @param PicoPageable|null $pageable
     * @param PicoSortable|string|null $sortable
     * @return array|null
     * @throws EntityException|EmptyResultException
     */
    public function findSpecific($selected, $specification, $pageable = null, $sortable = null)
    {
        $data = null;
        $result = array();
        $info = $this->getTableInfo();
        $sqlQuery = $this->findSpecificQuery($selected, $specification, $pageable, $sortable, $info);
    
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($this->matchRow($stmt))
            {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $data = array();
                foreach($rows as $row)                
                {
                    $data = $this->fixDataType($row, $info); 
                    $data = $this->join($data, $row, $info);
                    $result[] = $data;
                }
            }
            else
            {
                throw new EmptyResultException(self::MESSAGE_NO_RECORD_FOUND);
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
        return $result;
    }

    /**
     * Get all mathced record from database
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @param PicoPageable $pageable
     * @param PicoSortable|string $sortable
     * @return array|null
     * @throws PDOException|NoDatabaseConnectionException|EntityException
     */
    public function findBy($propertyName, $propertyValue, $pageable = null, $sortable = null)
    {
        $info = $this->getTableInfo();
        $where = $this->createWhereFromArgs($info, $propertyName, $propertyValue);
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $data = null;
        $result = array();
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName())
            ->where($where);
        if($pageable != null)
        {
            $sqlQuery = $this->setPagable($sqlQuery, $pageable);        
        }
        if($pageable != null || $sortable != null)
        {
            $sqlQuery = $this->setSortable($sqlQuery, $pageable, $sortable, $info);        
        }
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($this->matchRow($stmt))
            {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $data = array();
                foreach($rows as $row)                
                {
                    $data = $this->fixDataType($row, $info); 
                    $data = $this->join($data, $row, $info);
                    $result[] = $data;
                }
            }
        }
        catch(PDOException $e)
        {
            throw new PDOException($e->getMessage());
        }
        catch(NoDatabaseConnectionException $e)
        {
            throw new NoDatabaseConnectionException($e->getMessage());
        }
        catch(Exception $e)
        {
            // do nothing
        }
        return $result;
    }
    
    /**
     * Check if record is exists or not
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return boolean
     */
    public function existsBy($propertyName, $propertyValue)
    {
        return $this->countBy($propertyName, $propertyValue) > 0;
    }

    /**
     * Get all record from database wihout filter
     *
     * @param PicoSpecification $specification
     * @return integer
     * @throws EntityException|EmptyResultException
     */
    public function countAll($specification)
    {
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName());
        
        if($specification->isRequireJoin())
        {
            $sqlQuery = $this->addJoinQuery($sqlQuery, $info);
        }       
        if($specification != null)
        {
            $sqlQuery = $this->setSpecification($sqlQuery, $specification, $info);
        }
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            return $stmt->rowCount();
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
    }
    
    /**
     * Count data
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return integer
     * @throws EntityException|InvalidFilterException|PDOException|EmptyResultException
     */
    public function countBy($propertyName, $propertyValue)
    {
        $info = $this->getTableInfo();
        $primaryKeys = array_values($info->getPrimaryKeys());
        if(is_array($primaryKeys) && isset($primaryKeys[0][self::KEY_NAME]))
        {
            // it will be faster than asterisk
            $agg = $primaryKeys[0][self::KEY_NAME];
        }
        else
        {
            $agg = "*";
        }
        $where = $this->createWhereFromArgs($info, $propertyName, $propertyValue);
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($agg)
            ->from($info->getTableName())
            ->where($where);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($stmt != null)
            {
                return $stmt->rowCount();
            }
            else
            {
                throw new PDOException("Unknown error");
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
    }
    
    /**
     * Delete record from database without read it first
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return integer
     * @throws EntityException|InvalidFilterException|PDOException|EmptyResultException
     */
    public function deleteBy($propertyName, $propertyValue)
    {
        $info = $this->getTableInfo();
        $where = $this->createWhereFromArgs($info, $propertyName, $propertyValue);
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->delete()
            ->from($info->getTableName())
            ->where($where);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($stmt != null)
            {
                return $stmt->rowCount();
            }
            else
            {
                throw new PDOException("Unknown error");
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
    }
    
    /**
     * Get one mathced record from database
     *
     * @param string $propertyName
     * @param array $propertyValues
     * @param PicoSortable|string|null $sortable
     * @return array|null
     * @throws EntityException|InvalidFilterException|EmptyResultException
     */
    public function findOneBy($propertyName, $propertyValue, $sortable = null)
    {
        $info = $this->getTableInfo();
        $where = $this->createWhereFromArgs($info, $propertyName, $propertyValue);
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $data = null;
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName())
            ->where($where);
        $sqlQuery = $this->setSortable($sqlQuery, null, $sortable, $info);  
        $sqlQuery->limit(1)
            ->offset(0);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($this->matchRow($stmt))
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $data = $this->fixDataType($row, $info);               
                $data = $this->join($data, $row, $info);
            }
            else
            {
                $data = null;
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
        return $data;   
    }
    
    /**
     * Delete one mathced record from database
     *
     * @param string $propertyName
     * @param array $propertyValues
     * @return PDOStatement|boolean
     * @throws EntityException|InvalidFilterException|EmptyResultException
     */
    public function deleteOneBy($propertyName, $propertyValue)
    {
        $info = $this->getTableInfo();
        $where = $this->createWhereFromArgs($info, $propertyName, $propertyValue);
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $stmt = null;
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->delete()
            ->from($info->getTableName())
            ->where($where);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
        return $stmt;   
    }

    /**
     * Get real class name
     *
     * @param string $classNameJoin
     * @return string
     */
    private function getRealClassName($classNameJoin)
    {
        $result = $classNameJoin;
        if(stripos($classNameJoin, self::NAMESPACE_SEPARATOR) === false)
        {
            if(!$this->processClassList)
            {
                // processed once
                $reflect = new ExtendedReflectionClass($this->className);
                $useStatements = $reflect->getUseStatements(); 
                $this->namespaceName = $reflect->getNamespaceName();
                if(isset($useStatements) && is_array($useStatements))
                {
                    foreach($useStatements as $val)
                    {
                        $as = $val['as'];
                        $cls = $val['class'];
                        $this->importedClassList[$as] = $cls;
                    }
                }
            }
            if(isset($this->importedClassList[$classNameJoin]))
            {
                // get from map
                $result = $this->importedClassList[$classNameJoin];
            }
            else if(stripos($classNameJoin, self::NAMESPACE_SEPARATOR) === false)
            {
                // assumpt has same namespace
                $result = rtrim($this->namespaceName, self::NAMESPACE_SEPARATOR).self::NAMESPACE_SEPARATOR. $classNameJoin;
            }
        }
        return $result;
    }
    
    /**
     * Get reference column name
     *
     * @param array $join
     * @return string
     */
    private function getReferenceColumnName($join)
    {
        if(isset($join[self::KEY_REFERENCE_COLUMN_NAME]))
        {
             return $join[self::KEY_REFERENCE_COLUMN_NAME];
        }
        else
        {
            return $join[self::KEY_NAME];
        }
    }

    /**
     * Undocumented function
     *
     * @param array $row
     * @param string $referenceColumName
     * @return string|null
     */
    private function getJoinKey($row, $referenceColumName)
    {
        return isset($row[$referenceColumName]) ? $row[$referenceColumName] : null;
    }

    /**
     * Prepare join cache
     *
     * @param string $classNameJoin
     * @return void
     */
    private function prepareJoinCache($classNameJoin)
    {
        if(!isset($this->joinCache[$classNameJoin]))
        {
            $this->joinCache[$classNameJoin] = array();
        }
    }

    /**
     * Get join data
     *
     * @param string $classNameJoin
     * @param string $joinKey
     * @return MagicObject
     */
    private function getJoinData($classNameJoin, $joinKey)
    {
        if(isset($joinKey) && !isset($this->joinCache[$classNameJoin][$joinKey]))
        {              
            $className = $this->getRealClassName($classNameJoin);
            $obj = new $className(null, $this->database);
            $obj->find(array($joinKey)); 
            $this->joinCache[$classNameJoin][$joinKey] = $obj;
        }
        else
        {
            $obj = $this->joinCache[$classNameJoin][$joinKey];
        }
        return $obj;
    }
    
    /**
     * Join data by annotation @JoinColumn
     * 
     * @param mixed $data Object
     * @param array $row Row
     * @param PicoTableInfo $info Table information
     * @return object
     */
    private function join($data, $row, $info)
    {
        if(!empty($info->getJoinColumns()))
        {
            foreach($info->getJoinColumns() as $propName=>$join)
            {
                $referenceColumName = $this->getReferenceColumnName($join);
                $classNameJoin = $join[self::KEY_PROPERTY_TYPE];
                $joinKey = $this->getJoinKey($row, $referenceColumName);
                try
                {
                    $this->prepareJoinCache($classNameJoin);
                    $obj = $this->getJoinData($classNameJoin, $joinKey);
                    if(is_array($data))
                    {                       
                        $data[$propName] = $obj;
                    }
                    else
                    {
                        $data->{$propName} = $obj;
                    }
                }
                catch(Exception $e)
                {
                    // set null
                    if(is_array($data))
                    {
                        $data[$propName] = null;
                    }
                    else
                    {
                        $data->{$propName} = null;
                    }
                }
            }
        }
        return $data;
    }
    
    /**
     * Check if filter is valid or not
     *
     * @param string $filter
     * @return boolean
     */
    private function isValidFilter($filter)
    {
        return $this->notNullAndNotEmptyAndNotSpace($filter);
    }

    /**
     * Check if data is not null and not empty and not a space
     *
     * @param string $value
     * @return boolean
     */
    private function notNullAndNotEmptyAndNotSpace($value)
    {
        return $value != null && !empty(trim($value));
    }

    /**
     * Fix data type
     *
     * @param array $data
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function fixDataType($data, $info)
    {
        $result = array();
        $typeMap = $this->createTypeMap($info);
        foreach($data as $columnName=>$value)
        {
            if(isset($typeMap[$columnName]))
            {
                $result[$columnName] = $this->fixData($value, $typeMap[$columnName]);
            }
        }
        return $result;
    }

    /**
     * Fix value
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    public function fixData($value, $type)
    {
        $typeLower = strtolower($type);
        /*
        Map of data type (MySQL)
        "double"=>"double",
        "float"=>"double",
        "bigint"=>"integer",
        "smallint"=>"integer",
        "tinyint(1)"=>"boolean",
        "tinyint"=>"integer",
        "int"=>"integer",
        "varchar"=>"string",
        "char"=>"string",
        "tinytext"=>"string",
        "mediumtext"=>"string",
        "longtext"=>"string",
        "text"=>"string",   
        "enum"=>"string",   
        "bool"=>"boolean",
        "boolean"=>"boolean",
        "timestamp"=>"string",
        "datetime"=>"string",
        "date"=>"string",
        "time"=>"string"
        */     
        
        if($type == 'DateTime')
        {
            $v = strlen($value) > 19 ? substr($value, 0, 19) : $value;
            $ret = DateTime::createFromFormat(self::SQL_DATETIME_FORMAT, $v);
        }
        else if($typeLower == 'bool' || $typeLower == 'boolean')
        {
            $ret = $this->boolval($value);
        }
        else if($typeLower == 'integer')
        {
            $ret = $this->intval($value);
        }
        else if($typeLower == 'double')
        {
            $ret = $this->doubleval($value);
        }
        else if($this->isDateTimeNull($value))
        {
            $ret = null;
        }
        else
        {
            $ret = $value;
        }
        return $ret;
    }
    
    /**
     * Boolean value
     *
     * @param mixed $value
     * @return boolean
     */
    private function boolval($value)
    {
        return $value == 1 || $value == '1';
    }
    
    /**
     * Integer value
     *
     * @param mixed $value
     * @return mixed
     */
    private function intval($value)
    {
        if($value === null)
        {
            $ret = null;
        }
        else
        {
            $ret = intval($value);
        }
        return $ret;
    }
    
    /**
     * Double value
     *
     * @param mixed $value
     * @return mixed
     */
    private function doubleval($value)
    {
        if($value === null)
        {
            $ret = null;
        }
        else
        {
            $ret = doubleval($value);
        }
        return $ret;
    }

    /**
     * Fixing input
     *
     * @param mixed $value
     * @param array $column
     * @return mixed
     */
    private function fixInput($value, $column)
    {
        if($value instanceof DateTime)
        {
            if(isset($column[self::DATE_TIME_FORMAT]))
            {
                return (string) $value->format($column[self::DATE_TIME_FORMAT]);
            }
            else
            {
                return (string) $value->format(self::SQL_DATETIME_FORMAT);
            }
        }
        return $value;
    }
    
    /**
     * Check if date time is NULL
     * @param string $value
     * @return boolean
     */
    private function isDateTimeNull($value)
    {
        if(!isset($value) || !is_string($value))
        {
            return false;
        }
        $value = str_replace("T", " ", $value);
        if(strlen($value) > 26)
        {
            $value = substr($value, 0, 26);
        }
        return $value == '0000-00-00 00:00:00.000000' 
            || $value == '0000-00-00 00:00:00.000' 
            || $value == '0000-00-00 00:00:00'
            || $value == '0000-00-00'
            ;
    }

    /**
     * Create type map
     *
     * @param PicoTableInfo $info Table information
     * @return array
     */
    private function createTypeMap($info)
    {
        $map = array();
        if(isset($info) && $info->getColumns() != null)
        {
            foreach($info->getColumns() as $cols)
            {
                $map[$cols[self::KEY_NAME]] = $cols[self::KEY_PROPERTY_TYPE];
            }
        }
        return $map;
    }

    /**
     * Select record from database
     *
     * @return mixed
     * @throws EntityException
     */
    public function select()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_select($info, $queryBuilder, $where, $this->specification, $this->pageable, $this->sortable);
    }

    /**
     * Select all records from database
     *
     * @return mixed
     * @throws EntityException
     */
    public function selectAll()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_selectAll($info, $queryBuilder, $where, $this->specification, $this->pageable, $this->sortable);
    }

    /**
     * Query of select data
     *
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException
     */
    public function selectQuery()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_selectQuery($info, $queryBuilder, $where);
    }

    /**
     * Select record from database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return mixed
     * @throws EntityException|InvalidFilterException|EmptyResultException
     */
    private function _select($info = null, $queryBuilder = null, $where = null, $specification = null, $pageable = null, $sortable = null)
    {
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        if($where == null)
        {
            $where = $this->getWhere($info, $queryBuilder);
        }
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $data = null;
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName());

        if($this->isRequireJoin($specification, $pageable, $sortable, $info))
        {
            $sqlQuery = $this->addJoinQuery($sqlQuery, $info);
        }
        $sqlQuery->where($where);
        $sqlQuery->limit(1)->offset(0);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($this->matchRow($stmt))
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $data = $this->fixDataType($row, $info);
                $data = $this->join($data, $row, $info);
            }
            else
            {
                $data = null;
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
        return $data;
    }

    /**
     * Select record from database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return mixed
     * @throws EntityException|InvalidFilterException|EmptyResultException
     */
    private function _selectAll($info = null, $queryBuilder = null, $where = null, $specification = null, $pageable = null, $sortable = null)
    {
        $result = array();
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        if($where == null)
        {
            $where = $this->getWhere($info, $queryBuilder);
        }
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $data = null;
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName());

        if($this->isRequireJoin($specification, $pageable, $sortable, $info))
        {
            $sqlQuery = $this->addJoinQuery($sqlQuery, $info);
        }
        $sqlQuery->where($where);
        try
        {
            $stmt = $this->database->executeQuery($sqlQuery);
            if($this->matchRow($stmt))
            {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows as $row)
                {
                    $data = $this->fixDataType($row, $info);
                    $data = $this->join($data, $row, $info);
                    $result[] = $data;
                }
            }
            else
            {
                $result = array();
            }
        }
        catch(Exception $e)
        {
            throw new EmptyResultException($e->getMessage());
        }
        return $result;
    }

    /**
     * Select record from database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException|InvalidFilterException
     */
    private function _selectQuery($info = null, $queryBuilder = null, $where = null)
    {
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        if($where == null)
        {
            $where = $this->getWhere($info, $queryBuilder);
        }
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        return $queryBuilder
            ->newQuery()
            ->select($info->getTableName().".*")
            ->from($info->getTableName())
            ->where($where);
    }

    /**
     * Update data
     *
     * @param boolean $includeNull
     * @return PDOStatement
     * @throws EntityException
     */
    public function update($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_update($info, $queryBuilder, $where);
    }

    /**
     * Query of update data
     *
     * @param boolean $includeNull
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException
     */
    public function updateQuery($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_updateQuery($info, $queryBuilder, $where);
    }

    /**
     * Update record on database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PDOStatement
     * @throws InvalidFilterException
     */
    private function _update($info = null, $queryBuilder = null, $where = null)
    {
        $sqlQuery = $this->_updateQuery($info, $queryBuilder, $where);
        return $this->database->executeUpdate($sqlQuery);
    }

    /**
     * Update record on database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PicoDatabaseQueryBuilder
     * @throws InvalidFilterException|EntityException
     */
    private function _updateQuery($info = null, $queryBuilder = null, $where = null)
    {
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        if($where == null)
        {
            $where = $this->getWhere($info, $queryBuilder);
        }
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }
        $set = $this->getSet($info, $queryBuilder);
        return $queryBuilder
            ->newQuery()
            ->update($info->getTableName())
            ->set($set)
            ->where($where);
    }

    /**
     * Delete record from database
     *
     * @return PDOStatement
     * @throws EntityException
     */
    public function delete()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_delete($info, $queryBuilder, $where);
    }

    /**
     * Query of delete record
     *
     * @return PicoDatabaseQueryBuilder
     * @throws EntityException
     */
    public function deleteQuery()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_deleteQuery($info, $queryBuilder, $where);
    }

    /**
     * Delete record from database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PDOStatement
     */
    private function _delete($info = null, $queryBuilder = null, $where = null)
    {
        $sqlQuery = $this->_deleteQuery($info, $queryBuilder, $where);
        return $this->database->executeDelete($sqlQuery);
    }

    /**
     * Delete record from database with primary keys given
     *
     * @param PicoTableInfo $info Table information
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PicoDatabaseQueryBuilder
     * @throws InvalidFilterException|EntityException
     */
    private function _deleteQuery($info = null, $queryBuilder = null, $where = null)
    {
        if($queryBuilder == null)
        {
            $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        }
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        if($where == null)
        {
            $where = $this->getWhere($info, $queryBuilder);
        }
        if(!$this->isValidFilter($where))
        {
            throw new InvalidFilterException(self::MESSAGE_INVALID_FILTER);
        }              
        return $queryBuilder
            ->newQuery()
            ->delete()
            ->from($info->getTableName())
            ->where($where);
    }

    /**
     * Get MagicObject with WHERE specification
     *
     * @param PicoSpecification $specification
     * @return PicoDatabasePersistenceExtended
     */
    public function whereWithSpecification($specification)
    {
        $persist = new PicoDatabasePersistenceExtended($this->database, $this->object);
        
        
        $persist->specification = $specification;

        $sqlQuery = new PicoDatabaseQueryBuilder($this->database);
        $info = $persist->getTableInfo();
        
        try
        {
            if($persist->isRequireJoin($specification, null, null, $info))
            {
                $persist->addJoinQuery($sqlQuery, $info);
            }

            $persist->whereStr = $persist->createWhereFromSpecification($sqlQuery, $specification, $info);
            $persist->whereIsDefinedFirst = true;
        }
        catch(Exception $e)
        {
            // Do nothing
        }

        return $persist;
    }
}