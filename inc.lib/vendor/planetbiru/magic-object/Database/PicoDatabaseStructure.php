<?php

namespace MagicObject\Database;

use MagicObject\Exceptions\MandatoryTableNameException;
use MagicObject\MagicObject;
use MagicObject\Util\PicoAnnotationParser;
use stdClass;

class PicoDatabaseStructure
{
    const ANNOTATION_TABLE = "Table";
    const ANNOTATION_COLUMN = "Column";  
    const ANNOTATION_ID = "Id";  
    const KEY_NAME = "name";
    const KEY_TYPE = "type";
    const KEY_NULL = "null";
    const KEY_NOT_NULL = "notnull";
    const KEY_NULLABLE = "nullable";
    const KEY_PRIMARY = "primary";
    
    const DATABASE_TYPE_MYSQL = "mysql";
    const DATABASE_TYPE_MARIADB = "mariadb";
    
    /**
     * Object
     *
     * @var MagicObject
     */
    private $object;
    
    /**
     * Class name
     *
     * @var string
     */
    private $className = "";
    public function __construct($object)
    {
        $this->className = get_class($object);
        $this->object = $object;
    }
    
    
    /**
     * Show create table
     *
     * @param string $databaseType
     * @param string $tableName
     * @return string
     */
    public function showCreateTable($databaseType, $tableName = null)
    {
        $info = $this->getObjectInfo();
        if (!isset($tableName) && !isset($info->tableName)) 
        {
            throw new MandatoryTableNameException("Table name is mandatory");
        }
        if (isset($tableName) && !isset($info->tableName)) 
        {
            $tableName = $info->tableName;
        }
        $createStrArr = array();
        $createStrArr[] = "CREATE TABLE IF NOT EXISTS $tableName(";
        $createStrArr[] = $this->showCreateTableByType($databaseType, $info);
        $createStrArr[] = ");";
        return implode("\r\n", $createStrArr);
    }
    
    /**
     * Show create table
     *
     * @param string $databaseType
     * @param stdClass $info
     * @return string
     */
    private function showCreateTableByType($databaseType, $info)
    {
        $createStrArr = array();
        $pk = array();
        if($databaseType == self::DATABASE_TYPE_MYSQL) 
        {
            foreach($info->column as $column) 
            {
                $createStrArr[] = $column[self::KEY_NAME]." ".$column[self::KEY_TYPE]." ".$this->nullable($column[self::KEY_NULLABLE]);
            }
            foreach($info->column as $column) 
            {
                if(isset($column[self::KEY_PRIMARY]) && $column[self::KEY_PRIMARY] === true)
                {
                    $pk[] = $column[self::KEY_NAME];
                }
            }
            if(!empty($pk))
            {
                $createStrArr[] = "PRIMARY KEY (".implode(", ", $pk).")";
            }
        }
        
        return implode(",\r\n", $createStrArr);
    }
    
    /**
     * Create nullable
     *
     * @param mixed $nullable
     * @return string
     */
    private function nullable($nullable)
    {
        if($nullable === true || strtolower($nullable) == "true")
        {
            return "NULL";
        }
        else
        {
            return "NOT NULL";
        }
    }
    
    /**
     * Get object information
     *
     * @return stdClass
     */
    public function getObjectInfo(){
        $reflexClass = new PicoAnnotationParser($this->className);
        $table = $reflexClass->getParameter(self::ANNOTATION_TABLE);
        $values = $reflexClass->parseKeyValue($table);
        $picoTableName = $values[self::KEY_NAME];
        $columns = array();
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
                    $columns[$prop->name] = $values;
                }
            }
            foreach($parameters as $param=>$val)
            {
                if(strcasecmp($param, self::ANNOTATION_ID) == 0 && isset($columns[$prop->name]))
                {
                    $columns[$prop->name][self::KEY_PRIMARY] = true;
                }
            }
        }
        $info = new stdClass();
        $info->name = $picoTableName;
        $info->tableName = $picoTableName;
        $info->columns = $columns;
        $info->primaryKeys = $primaryKeys;
        $info->autoIncrementKeys = $autoIncrementKeys;
        $info->notNullColumns = $notNullColumns;
        $info->defaultValue = $defaultValue;
        return $info;
    }
}