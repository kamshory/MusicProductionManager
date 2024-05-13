<?php

namespace MagicObject\Database;

class PicoEntityField
{
    /**
     * Entity
     *
     * @var string
     */
    private $entity = null;
    
    /**
     * Field
     *
     * @var string
     */
    private $field = null;
    
    /**
     * Parent field
     *
     * @var string
     */
    private $parentField = null;

    /**
     * Get entity
     *
     * @return  string
     */ 
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get field
     *
     * @return  string
     */ 
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * Get parent field
     *
     * @return  string
     */ 
    public function getParentField()
    {
        return $this->parentField;
    }
    
    /**
     * Constructor
     *
     * @param string $field
     */
    public function __construct($field)
    {
        if(strpos($field, "."))
        {
            $arr = explode(".", $field, 2);
            $this->field = $arr[1];
            $this->entity = $arr[0];
            $this->parentField = $arr[0];
        }
        else
        {
            $this->field = $field;
        }
    }

    
}