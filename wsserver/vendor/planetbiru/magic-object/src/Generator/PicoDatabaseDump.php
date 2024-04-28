<?php

namespace MagicObject\Generator;

use MagicObject\Database\PicoDatabasePersistence;
use MagicObject\Database\PicoDatabaseType;
use MagicObject\Database\PicoPageData;
use MagicObject\Database\PicoTableInfo;
use MagicObject\MagicObject;
use MagicObject\Util\Database\PicoDatabaseUtilMySql;

class PicoDatabaseDump
{
    /**
     * Table info
     *
     * @var PicoTableInfo
     */
    private $tableInfo;    
    
    /**
     * Table name
     *
     * @var string
     */
    private $picoTableName = "";
    
    
    /**
     * Columns
     *
     * @var array
     */
    private $columns = array();

    /**
     * Dump table structure
     *
     * @param MagicObject $entity Entity to be dump
     * @param string $databaseType Target database type
     * @param boolean $createIfNotExists Add DROP TABLE IF EXISTS before create table
     * @param boolean $dropIfExists Add IF NOT EXISTS on create table
     * @param string $engine Storage engine
     * @param string $charset Default charset
     * @return string
     */
    public function dumpStructure($entity, $databaseType, $createIfNotExists = false, $dropIfExists = false, $engine = 'InnoDB', $charset = 'utf8mb4')
    {
        $databasePersist = new PicoDatabasePersistence(null, $entity);
        $tableInfo = $databasePersist->getTableInfo();
        $picoTableName = $tableInfo->getTableName();
        
        if($databaseType == PicoDatabaseType::DATABASE_TYPE_MARIADB || $databaseType == PicoDatabaseType::DATABASE_TYPE_MYSQL)
        {
            return PicoDatabaseUtilMySql::dumpStructure($tableInfo, $picoTableName, $createIfNotExists, $dropIfExists, $engine, $charset);
        }
        else
        {
            return "";
        }
    }
    /**
     * Get entity table info 
     *
     * @param MagicObject $entity
     * @return PicoTableInfo|null
     */
    public function getTableInfo($entity)
    {
        if($entity != null)
        {
            return $entity->tableInfo();
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Update query alter table add column
     *
     * @param string $query
     * @param string $lastColumn
     * @param string $databaseType
     * @return string
     */
    public function updateQueryAlterTableAddColumn($query, $lastColumn, $databaseType)
    {
        if($lastColumn != null && ($databaseType == PicoDatabaseType::DATABASE_TYPE_MYSQL || $databaseType == PicoDatabaseType::DATABASE_TYPE_MARIADB))
        {
            $query = " AFTER ".$lastColumn;
        }
        return $query;
    }
    
    /**
     * Create query ALTER TABLE ADD COLUMN
     *
     * @param MagicObject $entity
     * @return string[]
     */
    public function createAlterTableAdd($entity)
    {
        $tableInfo = $this->getTableInfo($entity);
        $queryAlter = array();
        if($tableInfo != null)
        {
            $dbColumnNames = array();
            
            $database = $entity->currentDatabase();
            $rows = PicoColumnGenerator::getColumnList($database, $tableInfo->getTableName());

            if(is_array($rows))
            {
                foreach($rows as $row)
                {
                    $columnName = $row['Field'];
                    $dbColumnNames[] = $columnName;
                }
            }
            $lastColumn = null;
            foreach($tableInfo->getColumns() as $entityColumn)
            {
                if(!in_array($entityColumn['name'], $dbColumnNames))
                {
                    $query = "ALTER TABLE ADD COLUMN ".$entityColumn['name']." ".$entityColumn['type'];
                    $query = $this->updateQueryAlterTableAddColumn($query, $lastColumn, $database->getDatabaseType());
                    $queryAlter[]  = $query;
                    $lastColumn = $entityColumn['name'];
                }
            }
        }
        return $queryAlter;
    }
    
    /**
     * Dump data to SQL. 
     * WARNING!!! Use different instance to dump different entity
     *
     * @param MagicObject|PicoPageData $data Data to be dump
     * @param string $databaseType Target database type
     * @return string
     */
    public function dumpData($data, $databaseType)
    {
        if(!isset($this->tableInfo))
        {
            $entity = null;
            if($data instanceof PicoPageData && isset($data->getResult()[0]))
            {
                $entity = $data->getResult()[0];
            }
            else if($data instanceof MagicObject)
            {
                $entity = $data;
            }
            else if(is_array($data) && isset($data[0]) && $data[0] instanceof MagicObject)
            {
                $entity = $data[0];
            }
            if($entity == null)
            {
                return "";
            }
            
            $databasePersist = new PicoDatabasePersistence(null, $entity);
            $this->tableInfo = $databasePersist->getTableInfo();
            $this->picoTableName = $this->tableInfo->getTableName();
            $this->columns = $this->tableInfo->getColumns();
        }
        
        if($databaseType == PicoDatabaseType::DATABASE_TYPE_MARIADB || $databaseType == PicoDatabaseType::DATABASE_TYPE_MYSQL)
        {
            return PicoDatabaseUtilMySql::dumpData($this->columns, $this->picoTableName, $data);
        }
        else
        {
            return "";
        }
    }
    
    
    
}