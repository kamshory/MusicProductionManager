<?php
namespace MagicObject\Database;

use DateTime;
use Exception;
use PDO;
use PDOException;
use PDOStatement;
use MagicObject\Exceptions\EmptyResultException;
use MagicObject\Exceptions\InvalidFilterException;
use MagicObject\Exceptions\NoInsertableColumnException;
use MagicObject\Exceptions\NoColumnMatchException;
use MagicObject\Exceptions\NoUpdatableColumnException;
use MagicObject\Exceptions\NoPrimaryKeyDefinedException;
use MagicObject\Util\ExtendedReflectionClass;
use MagicObject\Util\PicoAnnotationParser;
use stdClass;

class PicoDatabasePersistent // NOSONAR
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

    const ORDER_ASC = "ASC";
    const ORDER_DESC = "DESC";

    const MESSAGE_NO_RECORD_FOUND = "No record found";
    const MESSAGE_INVALID_FILTER = "Invalid filter";
    const SQL_DATETIME_FORMAT = "Y-m-d H:i:s";
    const DATE_TIME_FORMAT = "datetimeformat";
    
    const NAMESPACE_SEPARATOR = "\\";
    
    /**
     * Database connection
     *
     * @var PicoDatabase
     */
    private $database;

    /**
     * Object
     *
     * @var mixed
     */
    private $object;

    /**
     * Class name
     * @var string
     */
    private $className = "";

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
     * Database connection
     *
     * @param PicoDatabase $database
     * @param mixed $object
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
     * @param bool $skip
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
     * Get table information by parsing class and property annotation
     *
     * @return stdClass
     */
    public function getTableInfo() // NOSONAR
    {
        $reflexClass = new PicoAnnotationParser($this->className);
        $table = $reflexClass->getParameter(self::ANNOTATION_TABLE);
        $values = $reflexClass->parseKeyValue($table);
        $picoTableName = $values[self::KEY_NAME];
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
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_COLUMN) == 0)
                {
                    $values = $reflexProp->parseKeyValue($val);
                    if(!empty($values))
                    {
                        $columns[$prop->name] = $values;
                    }
                }
            }
            // set column type
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_VAR) == 0 && isset($columns[$prop->name]))
                {
                    $type = explode(' ', trim($val, " \r\n\t "))[0];
                    $columns[$prop->name][self::KEY_PROPERTY_TYPE] = $type;
                }
                if(strcasecmp($param, self::SQL_DATE_TIME_FORMAT) == 0)
                {
                    $values = $reflexProp->parseKeyValue($val);
                    if(isset($values['pattern']))
                    {
                        $columns[$prop->name][self::DATE_TIME_FORMAT] = $values['pattern'];
                    }
                }
            }
            
            // get join column name of each parameters
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_JOIN_COLUMN) == 0)
                {
                    $values = $reflexProp->parseKeyValue($val);
                    if(!empty($values))
                    {
                        $joinColumns[$prop->name] = $values;
                    }
                }
            }
            
            // set join column type
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_VAR) == 0 && isset($joinColumns[$prop->name]))
                {
                    $type = explode(' ', trim($val, " \r\n\t "))[0];
                    $joinColumns[$prop->name][self::KEY_PROPERTY_TYPE] = $type;
                    $joinColumns[$prop->name][self::KEY_ENTITY_OBJECT] = true;
                }
            }          

            // list primary key
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_ID) == 0 && isset($columns[$prop->name]))
                {
                    $primaryKeys[$prop->name] = array(self::KEY_NAME=>$columns[$prop->name][self::KEY_NAME]);
                }
            }

            // list autogenerated column
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_GENERATED_VALUE) == 0 && isset($columns[$prop->name]))
                {
                    $vals = $reflexClass->parseKeyValue($val);
                    $autoIncrementKeys[$prop->name] = array(
                        self::KEY_NAME=>isset($columns[$prop->name][self::KEY_NAME])?$columns[$prop->name][self::KEY_NAME]:null,
                        self::KEY_STRATEGY=>isset($vals[self::KEY_STRATEGY])?$vals[self::KEY_STRATEGY]:null,
                        self::KEY_GENERATOR=>isset($vals[self::KEY_GENERATOR])?$vals[self::KEY_GENERATOR]:null
                    );
                }
            }
            
            // define default column value
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_DEFAULT_COLUMN) == 0)
                {
                    $vals = $reflexClass->parseKeyValue($val);
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

            // list not null column
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_NOT_NULL) == 0 && isset($columns[$prop->name]))
                {
                    $notNullColumns[$prop->name] = array(self::KEY_NAME=>$columns[$prop->name][self::KEY_NAME]);
                }
            }
        }
        // bring it together
        $info = new stdClass;
        $info->tableName = $picoTableName;
        $info->columns = $columns;
        $info->joinColumns = $joinColumns;
        $info->primaryKeys = $primaryKeys;
        $info->autoIncrementKeys = $autoIncrementKeys;
        $info->defaultValue = $defaultValue;
        $info->notNullColumns = $notNullColumns;
        return $info;
    }

    /**
     * Get match row
     *
     * @param PDOStatement $stmt
     * @return bool
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
     * @param bool $includeNull
     * @return PDOStatement
     */
    public function save($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
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
     * Get object values
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return array
     */
    private function getValues($info, $queryBuilder)
    {
        $values = array();
        foreach($info->columns as $property=>$column)
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
     * @param stdClass $info
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
                if($val === true && isset($info->columns[$key]))
                {
                    $columnName = $info->columns[$key][self::KEY_NAME];
                    $nullCols[] = $columnName;
                }
            }
        }
        return $nullCols;
    }
    
    /**
     * Get noninsertable column
     * @param stdClass $info
     * @return array
     */
    private function getNonInsertableCols($info)
    {
        $nonInsertableCols = array();
        foreach($info->columns as $params)
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
     * @param stdClass $info
     * @return array
     */
    private function getNonUpdatableCols($info)
    {
        $nonUpdatableCols = array();
        foreach($info->columns as $params)
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
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return string
     */
    private function getSet($info, $queryBuilder)
    {
        $sets = array();
        $primaryKeys = $this->getPrimaryKeys($info);
        $nullCols = $this->getNullCols($info);
        $nonUpdatableCols = $this->getNonUpdatableCols($info);
        foreach($info->columns as $property=>$column)
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
        return implode(", ", $sets);
    }

    /**
     * Get WHERE statement
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return string
     */
    private function getWhere($info, $queryBuilder)
    {
        $wheres = array();
        foreach($info->primaryKeys as $property=>$column)
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
     * @param stdClass $info
     * @return array
     */
    public function getPrimaryKeys($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $primaryKeys = array();
        foreach($info->primaryKeys as $column)
        {
            $primaryKeys[] = $column[self::KEY_NAME];
        }
        return $primaryKeys;
    }

    /**
     * Get columns
     *
     * @param stdClass $info
     * @return array
     */
    public function getColumns($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $columns = array();
        foreach($info->columns as $column)
        {
            $columns[] = $column[self::KEY_NAME];
        }
        return $columns;
    }

    /**
     * Get join columns
     *
     * @param stdClass $info
     * @return array
     */
    public function getJoinColumns($info = null)
    {
        if($info == null)
        {
            $info = $this->getTableInfo();
        }
        $joinColumns = array();
        foreach($info->joinColumns as $joinColumn)
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
     * @return bool
     */
    public function isPrimaryKeys($columnName, $primaryKeys)
    {
        return in_array($columnName, $primaryKeys);
    }

    /**
     * Get primary key with autoincrement value
     *
     * @param stdClass $info
     * @return array
     */
    public function getPrimaryKeyAutoIncrement($info)
    {
        $aiKeys = array();
        if(isset($info->autoIncrementKeys) && is_array($info->autoIncrementKeys))
        {
            $primaryKeys = array_keys($info->primaryKeys);
            foreach($info->autoIncrementKeys as $name=>$value)
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
     * @param stdClass $info
     * @param bool $fisrtCall
     * @return void
     */
    private function addGeneratedValue($info, $fisrtCall)
    {
        if(!$this->generatedValue)
        {
            $keys = $info->autoIncrementKeys;
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
     * @param bool $includeNull
     * @return PDOStatement
     */
    public function insert($includeNull = false)
    {
        $this->flagIncludeNull = $includeNull;
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        return $this->_insert($info, $queryBuilder);
    }

    /**
     * Insert data
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @return PDOStatement
     */
    private function _insert($info = null, $queryBuilder = null)
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
        
        
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->insert()
            ->into($info->tableName)
            ->fields($this->createStatementFields($fixValues))
            ->values($this->createStatementValues($fixValues));
        $stmt = $this->database->executeInsert($sqlQuery);
        if(!$this->generatedValue)
        {
            $this->addGeneratedValue($info, false);
            $this->object->update();
        }
        return $stmt;
    }
    
    /**
     * Fix insertable values
     *
     * @param array $values
     * @param stdClass $info
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
        
        if(isset($info->autoIncrementKeys))
        {
            foreach($info->autoIncrementKeys as $name=>$col)
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
     * @param stdClass $info
     * @return string
     */
    public function createStatementFields($values)
    {
        return "(".implode(", ", array_keys($values)).")";
    }

    /**
     * Implode array values to value list
     *
     * @param array $values
     * @return string
     */
    public function createStatementValues($values)
    {      
        return "(".implode(", ", array_values($values)).")";
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
        $source = str_replace("#And#And", "Or#Or", $source);


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
     * @param stdClass $info
     * @return array
     */
    private function getColumnMap($info)
    {
        $maps = array();
        if(isset($info->joinColumns) && is_array($info->joinColumns))
        {
            foreach($info->joinColumns as $key=>$value)
            {
                $maps[$key] = $value[self::KEY_NAME];
            }
        }
        if(isset($info->columns) && is_array($info->columns))
        {
            foreach($info->columns as $key=>$value)
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
        $columnNames = $this->getColumnNames($propertyName, $info->columns);
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
        return implode(" ", $wheres);
    }

    /**
     * Create WHERE from specification
     *
     * @param PicoDatabaseQueryBuilder $sqlQuery
     * @param PicoSpecification $specification
     * @param stdClass $info
     * @return string
     */
    private function createWhereFromSpecification($sqlQuery, $specification, $info)
    {
        $maps = $this->getColumnMap($info);
        $columnNames = array_values($maps);
        $arr = array();
        $arr[] = "(1=1)";
        if($specification != null && !$specification->isEmpty())
        {
            $specifications = $specification->getSpecifications();
            foreach($specifications as $spec)
            {           
                if($spec instanceof PicoPredicate)
                {
                    // flat
                    if(isset($maps[$spec->getFiled()]))
                    {
                        $arr[] = $spec->getFilterLogic() . " " . $maps[$spec->getFiled()] . " " . $spec->getComparation()->getComparison() . " " . $sqlQuery->escapeValue($spec->getValue());
                    }
                    else if(in_array($spec->getFiled(), $columnNames))
                    {
                        $arr[] = $spec->getFilterLogic() . " " . $spec->getFiled() . " " . $spec->getComparation()->getComparison() . " " . $sqlQuery->escapeValue($spec->getValue());
                    }
                }
                else if($spec instanceof PicoSpecification)
                {
                    // nested
                    $arr[] = $spec->getParentFilterLogic() . " (" . $this->createWhereFromSpecification($sqlQuery, $spec, $info) . ")";
                }
            }
        }
        $ret = implode(" ", $arr);
        return $this->trimWhere($ret);
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
            return $order->createOrderBy($info);
        }
        else if(is_string($order))
        {
            $orderBys = array();
            $pKeys = array_values($info->primaryKeys);
            if(!empty($pKeys))
            {
                foreach($pKeys as $pKey)
                {
                    $pKeyCol = $pKey[self::KEY_NAME];
                    $orderBys[] = $pKeyCol." ".strtolower($order);
                }
            }
            return implode(", ", $orderBys);
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Check if primary key has valid value or not
     *
     * @param string[] $primaryKeys
     * @param array $propertyValues
     * @return bool
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
     */
    public function find($propertyValues)
    {
        $data = null;
        $info = $this->getTableInfo();
        
        $primaryKeys = $info->primaryKeys;
        
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
                ->select($info->tableName.".*")
                ->from($info->tableName)
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
     * @param stdClass $info
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
     * @param PicoPagable $pagable
     * @return PicoDatabaseQueryBuilder
     */
    private function setPagable($sqlQuery, $pagable)
    {
        if($pagable instanceof PicoPagable)
        {
            $offsetLimit = $pagable->getOffsetLimit();
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
     * @param PicoPagable|null $pagable
     * @param PicoSortable|string|null $pagable
     * @param stdClass $info
     * @return PicoDatabaseQueryBuilder
     */
    private function setSortable($sqlQuery, $pagable, $sortable, $info)
    {
        if($sortable != null)
        {
            if($sortable instanceof PicoSortable)
            {
                $sortOrder = $sortable->createOrderBy($info);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
            else if(is_string($sortable))
            {
                $sortOrder = $this->createOrderBy($info, $sortable);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
        } 
        else if($pagable != null && $pagable instanceof PicoPagable)
        {
            $sortOrder = $pagable->createOrderBy($info);
            if($this->notNullAndNotEmpty($sortOrder))
            {
                $sqlQuery->orderBy($sortOrder);
            }
            else if(is_string($sortable))
            {
                $sortOrder = $this->createOrderBy($info, $sortable);
                $sqlQuery = $this->setOrdeBy($sqlQuery, $sortOrder);
            }
            $offsetLimit = $pagable->getOffsetLimit();
            if($offsetLimit != null)
            {
                $limit = $offsetLimit->getLimit();
                $offset = $offsetLimit->getOffset();
                $sqlQuery->limit($limit);
                $sqlQuery->offset($offset);
            }
        }
        else if(is_string($pagable))
        {
            $sortOrder = $this->createOrderBy($info, $pagable);
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
     * Get all record from database wihout filter
     *
     * @param PicoSpecification $specification
     * @param PicoPagable|null $pagable
     * @param PicoSortable|string|null $sortable
     * @return array|null
     */
    public function findAll($specification, $pagable = null, $sortable = null)
    {
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $data = null;
        $result = array();
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->tableName.".*")
            ->from($info->tableName);
        
        if($specification != null)
        {
            $sqlQuery = $this->setSpecification($sqlQuery, $specification, $info);
        }
        if($pagable != null)
        {
            $sqlQuery = $this->setPagable($sqlQuery, $pagable);      
        }
        if($pagable != null || $sortable != null)
        {
            $sqlQuery = $this->setSortable($sqlQuery, $pagable, $sortable, $info);        
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
     * @param PicoPagable $pagable
     * @param PicoSortable|string $sortable
     * @return array|null
     */
    public function findBy($propertyName, $propertyValue, $pagable = null, $sortable = null)
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
            ->select($info->tableName.".*")
            ->from($info->tableName)
            ->where($where);
        if($pagable != null)
        {
            $sqlQuery = $this->setPagable($sqlQuery, $pagable);        
        }
        if($pagable != null || $sortable != null)
        {
            $sqlQuery = $this->setSortable($sqlQuery, $pagable, $sortable, $info);        
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
     * Check if data is exists or not
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return bool
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
     */
    public function countAll($specification)
    {
        $info = $this->getTableInfo();
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->select($info->tableName.".*")
            ->from($info->tableName);       
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
     */
    public function countBy($propertyName, $propertyValue)
    {
        $info = $this->getTableInfo();
        $primaryKeys = array_values($info->primaryKeys);
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
            ->from($info->tableName)
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
     * Delete data from database without read it first
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @return integer
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
            ->from($info->tableName)
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
     * Get all mathced record from database
     *
     * @param string $propertyName
     * @param array $propertyValues
     * @param PicoSortable|string|null $sortable
     * @return array|null
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
            ->select($info->tableName.".*")
            ->from($info->tableName)
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
     * Join data by annotation @JoinColumn
     * 
     * @param mixed $data Object
     * @param array $row Row
     * @param stdClass $info Table info
     * @return object
     */
    private function join($data, $row, $info)
    {
        if(!empty($info->joinColumns))
        {
            foreach($info->joinColumns as $propName=>$join)
            {
                $joinName = $join[self::KEY_NAME];
                $classNameJoin = $join[self::KEY_PROPERTY_TYPE];
                try
                {
                    $className = $this->getRealClassName($classNameJoin);
                    $obj = new $className(null, $this->database);
                    $obj->find(array($row[$joinName])); 
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
     * @return bool
     */
    private function isValidFilter($filter)
    {
        return $this->notNullAndNotEmptyAndNotSpace($filter);
    }

    /**
     * Check if data is not null and not empty and not a space
     *
     * @param string $value
     * @return bool
     */
    private function notNullAndNotEmptyAndNotSpace($value)
    {
        return $value != null && !empty(trim($value));
    }

    /**
     * Fix data type
     *
     * @param array $data
     * @param stdClass $info
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
        "tinyint(1)"=>"bool",
        "tinyint"=>"integer",
        "int"=>"integer",
        "varchar"=>"string",
        "char"=>"string",
        "tinytext"=>"string",
        "mediumtext"=>"string",
        "longtext"=>"string",
        "text"=>"string",   
        "enum"=>"string",   
        "boolean"=>"bool",
        "bool"=>"bool",
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
        else if($typeLower == 'bool')
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
     * @return bool
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
     * @return bool
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
     * @param stdClass $info
     * @return array
     */
    private function createTypeMap($info)
    {
        $map = array();
        if(isset($info) && isset($info->columns))
        {
            foreach($info->columns as $cols)
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
     */
    public function select()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_select($info, $queryBuilder, $where);
    }

    /**
     * Select record from database with primary keys given
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return mixed
     */
    private function _select($info = null, $queryBuilder = null, $where = null)
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
            ->select($info->tableName.".*")
            ->from($info->tableName)
            ->where($where);
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
     * Update
     *
     * @param bool $includeNull
     * @return PDOStatement
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
     * Update record on database with primary keys given
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PDOStatement
     */
    private function _update($info = null, $queryBuilder = null, $where = null)
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
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->update($info->tableName)
            ->set($set)
            ->where($where);
        return $this->database->executeUpdate($sqlQuery);
    }

    /**
     * Delete record from database
     *
     * @return PDOStatement
     */
    public function delete()
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $info = $this->getTableInfo();
        $where = $this->getWhere($info, $queryBuilder);
        return $this->_delete($info, $queryBuilder, $where);
    }

    /**
     * Delete record from database with primary keys given
     *
     * @param stdClass $info
     * @param PicoDatabaseQueryBuilder $queryBuilder
     * @param string $where
     * @return PDOStatement
     */
    private function _delete($info = null, $queryBuilder = null, $where = null)
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
        $sqlQuery = $queryBuilder
            ->newQuery()
            ->delete()
            ->from($info->tableName)
            ->where($where);
        return $this->database->executeDelete($sqlQuery);
    }
}