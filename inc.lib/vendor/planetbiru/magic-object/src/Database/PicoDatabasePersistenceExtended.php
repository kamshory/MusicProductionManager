<?php

namespace MagicObject\Database;

use MagicObject\Exceptions\NoRecordFoundException;
use MagicObject\MagicObject;

/**
 * Database persistence extended
 *
 * This class extends the functionality of the PicoDatabasePersistence
 * by adding dynamic property setting through magic methods and enhanced
 * record selection capabilities.
 * 
 * @author Kamshory
 * @package MagicObject\Database
 * @link https://github.com/Planetbiru/MagicObject
 */
class PicoDatabasePersistenceExtended extends PicoDatabasePersistence
{
    /**
     * Magic method to handle undefined methods for setting properties.
     *
     * This method dynamically handles method calls that start with "set".
     * It allows setting properties of the object in a more flexible way,
     * using a consistent naming convention.
     *
     * Supported dynamic method:
     *
     * - `set<PropertyName>`: Sets the value of the specified property.
     *   - If the property name follows "set", the method extracts the property name
     *     and assigns the provided value to it.
     *   - If no value is provided, it sets the property to null.
     *   - Example: `$obj->setFoo($value)` sets the property `foo` to `$value`.
     * 
     * @param string $method The name of the method that was called.
     * @param mixed[] $params The parameters passed to the method, expected to be an array.
     * @return $this Returns the current instance for method chaining.
     */
    public function __call($method, $params)
    {
        if (strlen($method) > 3 && strncasecmp($method, "set", 3) === 0 && isset($params) && is_array($params)){
            $var = lcfirst(substr($method, 3));
            if(empty($params))
            {
                $params[0] = null;
            }
            $this->object->set($var, $params[0]);
            return $this;
        }
    }
    
    /**
     * Get the current database for the specified entity.
     *
     * This method retrieves the database connection associated with the 
     * provided entity. If the entity does not have an associated database 
     * or if the connection is not valid, it defaults to the object's 
     * primary database connection.
     *
     * @param MagicObject $entity The entity for which to get the database.
     * @return PicoDatabase The database connection for the entity.
     */
    private function currentDatabase($entity)
    {
        $dbEnt = $this->object->databaseEntity($entity);
        $db = null;
        if(isset($dbEnt))
        {
            $db = $dbEnt->getDatabase(get_class($entity));
        }
        if(!isset($db) || !$db->isConnected())
        {
            $db = $this->object->_database;
        }
        return $db;
    }

    /**
     * Select one record.
     *
     * This method retrieves a single record from the database.
     * If no record is found, a NoRecordFoundException is thrown.
     *
     * @return MagicObject The selected record as an instance of MagicObject.
     * @throws NoRecordFoundException If no record is found.
     */
    public function select()
    {
        $data = parent::select();
        if($data == null)
        {
            throw new NoRecordFoundException(parent::MESSAGE_NO_RECORD_FOUND);
        }
        $entity = new $this->className($data);
        $entity->currentDatabase($this->currentDatabase($entity));
        $entity->databaseEntity($this->object->databaseEntity());
        return $entity;
    }

    /**
     * Select all records.
     *
     * This method retrieves all records from the database.
     * If no records are found, a NoRecordFoundException is thrown.
     *
     * @return MagicObject[] An array of MagicObject instances representing all records.
     * @throws NoRecordFoundException If no records are found.
     */
    public function selectAll()
    {
        $collection = array();
        $result = parent::selectAll();

        if($result == null || empty($result))
        {
            throw new NoRecordFoundException(parent::MESSAGE_NO_RECORD_FOUND);
        }
        foreach($result as $data)
        {
            $entity = new $this->className($data);
            $entity->databaseEntity($this->object->databaseEntity());
            $collection[] = $entity;
        }
        return $collection;
    }
}