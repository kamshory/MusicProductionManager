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
     * Function format
     *
     * @var string
     */
    private $functionFormat = "%s";

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
     * Get function format
     *
     * @return  string
     */ 
    public function getFunctionFormat()
    {
        return $this->functionFormat;
    }
    
    /**
     * Constructor
     *
     * @param string $fieldRaw
     */
    public function __construct($fieldRaw)
    {
        $field = $this->extractField($fieldRaw);
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
    
    /**
     * Extract field from any function
     *
     * @param string $fieldRaw
     * @return string
     */
    public function extractField($fieldRaw)
    {
        if(strpos($fieldRaw, "(") === false)
        {
            $this->functionFormat = "%s";
            return $fieldRaw;
        }
        else
        {
            $pattern = '/(\((?:\(??[^\(]*?\)))/m'; //NOSONAR
            preg_match_all($pattern , $fieldRaw, $out);
            $field = trim($out[0][0], "()");
            $this->functionFormat = str_replace($field, "%s", $fieldRaw);
            return $field;
        }
    }
}