<?php

namespace MagicObject\Database;

class PicoTableInfo
{
    /**
     * Table name
     *
     * @var string
     */
    private $tableName = "";

    /**
     * Columns
     *
     * @var array
     */
    private $columns = array();

    /**
     * Join columns
     *
     * @var array
     */
    private $joinColumns = array();

    /**
     * Primary keys
     *
     * @var array
     */
    private $primaryKeys = array();

    /**
     * Auto increment keys
     *
     * @var array
     */
    private $autoIncrementKeys = array();

    /**
     * Default value keys
     *
     * @var array
     */
    private $defaultValue = array();

    /**
     * Not null columns
     *
     * @var array
     */
    private $notNullColumns = array();

    /**
     * Constructor
     *
     * @param string $picoTableName
     * @param array $columns
     * @param array $joinColumns
     * @param array $primaryKeys
     * @param array $autoIncrementKeys
     * @param array $defaultValue
     * @param array $notNullColumns
     */
    public function __construct($picoTableName, $columns, $joinColumns, $primaryKeys, $autoIncrementKeys, $defaultValue, $notNullColumns)
    {
        $this->tableName = $picoTableName;
        $this->columns = $columns;
        $this->joinColumns = $joinColumns;
        $this->primaryKeys = $primaryKeys;
        $this->autoIncrementKeys = $autoIncrementKeys;
        $this->defaultValue = $defaultValue;
        $this->notNullColumns = $notNullColumns;
    }

    /**
     * Get table name
     *
     * @return  string
     */ 
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set table name
     *
     * @param  string  $tableName  Table name
     *
     * @return  self
     */ 
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get columns
     *
     * @return  array
     */ 
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set columns
     *
     * @param  array  $columns  Columns
     *
     * @return  self
     */ 
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get join columns
     *
     * @return  array
     */ 
    public function getJoinColumns()
    {
        return $this->joinColumns;
    }

    /**
     * Set join columns
     *
     * @param  array  $joinColumns  Join columns
     *
     * @return  self
     */ 
    public function setJoinColumns(array $joinColumns)
    {
        $this->joinColumns = $joinColumns;

        return $this;
    }

    /**
     * Get primary keys
     *
     * @return  array
     */ 
    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * Set primary keys
     *
     * @param  array  $primaryKeys  Primary keys
     *
     * @return  self
     */ 
    public function setPrimaryKeys(array $primaryKeys)
    {
        $this->primaryKeys = $primaryKeys;

        return $this;
    }

    /**
     * Get auto increment keys
     *
     * @return  array
     */ 
    public function getAutoIncrementKeys()
    {
        return $this->autoIncrementKeys;
    }

    /**
     * Set auto increment keys
     *
     * @param  array  $autoIncrementKeys  Auto increment keys
     *
     * @return  self
     */ 
    public function setAutoIncrementKeys(array $autoIncrementKeys)
    {
        $this->autoIncrementKeys = $autoIncrementKeys;

        return $this;
    }

    /**
     * Get default value keys
     *
     * @return  array
     */ 
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set default value keys
     *
     * @param  array  $defaultValue  Default value keys
     *
     * @return  self
     */ 
    public function setDefaultValue(array $defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Get not null columns
     *
     * @return  array
     */ 
    public function getNotNullColumns()
    {
        return $this->notNullColumns;
    }

    /**
     * Set not null columns
     *
     * @param  array  $notNullColumns  Not null columns
     *
     * @return  self
     */ 
    public function setNotNullColumns(array $notNullColumns)
    {
        $this->notNullColumns = $notNullColumns;

        return $this;
    }
}