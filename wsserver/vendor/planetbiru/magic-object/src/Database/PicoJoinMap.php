<?php

namespace MagicObject\Database;

class PicoJoinMap
{
    /**
     * Property name name
     *
     * @var string
     */
    private $propertyName;
    
    /**
     * Column name
     *
     * @var string
     */
    private $columnName;
    
    /**
     * Entity name
     *
     * @var string
     */
    private $entity;
    
    /**
     * Table name
     *
     * @var string
     */
    private $joinTable;
    
    /**
     * Table alias
     *
     * @var string
     */
    private $joinTableAlias;
    
    /**
     * Constructor
     *
     * @param string $propertyName
     * @param string $columnName
     * @param string $entity
     * @param string $joinTable
     * @param string $joinTableAlias
     */
    public function __construct($propertyName, $columnName, $entity, $joinTable, $joinTableAlias)
    {
        $this->propertyName = $propertyName;
        $this->columnName = $columnName;
        $this->entity = $entity;
        $this->joinTable = $joinTable;
        $this->joinTableAlias = $joinTableAlias;
    }
    
    /**
     * Get property name name
     *
     * @return  string
     */ 
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get column name
     *
     * @return  string
     */ 
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * Get entity name
     *
     * @return  string
     */ 
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get table name
     *
     * @return  string
     */ 
    public function getJoinTable()
    {
        return $this->joinTable;
    }

    /**
     * Get table alias
     *
     * @return  string
     */ 
    public function getJoinTableAlias()
    {
        return $this->joinTableAlias;
    }

    
}