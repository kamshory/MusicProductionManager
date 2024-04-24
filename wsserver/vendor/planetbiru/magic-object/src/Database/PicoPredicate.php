<?php

namespace MagicObject\Database;

class PicoPredicate
{
    /**
     * @var string
     */
    private $field = "";

    /**
     * Value
     *
     * @var mixed
     */
    private $value = "";

    /**
     * Data comparation
     *
     * @var PicoDataComparation
     */
    private $comparation = null;

    /**
     * Filter logic
     *
     * @var string
     */
    private $filterLogic = null;
    
    /**
     * Return true if require real join table
     *
     * @return boolean
     */
    public function isRequireJoin()
    {
        return strpos($this->field, ".");
    }

    /**
     * Equals
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function equals($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::equals($value);
        return $this;
    }
    
    /**
     * Is NULL
     * @param string $field
     * @return self
     */
    public function isNull($field)
    {
        return $this->equals($field, null);
    }

    /**
     * Not equals
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function notEquals($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::NOT_EQUALS);
        return $this;
    }
    
    /**
     * Is Not NULL
     * @param string $field
     * @return self
     */
    public function isNotNull($field)
    {
        return $this->notEquals($field, null);
    }

    /**
     * Like
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function like($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::like($value);
        return $this;
    }

    /**
     * Not like
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function notLike($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::notLike($value);
        return $this;
    }

    /**
     * Less than
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function lessThan($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::LESS_THAN);
        return $this;
    }

    /**
     * Greater than
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function greaterThan($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::GREATER_THAN);
        return $this;
    }

    /**
     * Less than or equals
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function lessThanOrEquals($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::LESS_THAN_OR_EQUALS);
        return $this;
    }

    /**
     * Greater than or equals
     * @param string $field
     * @param mixed $value
     * @return self
     */
    public function greaterThanOrEquals($field, $value)
    {
        $this->field = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::GREATER_THAN_OR_EQUALS);
        return $this;
    }

    /**
     * Get the value of field
     *
     * @return string
     */ 
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get value
     *
     * @return mixed
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get data comparation
     *
     * @return PicoDataComparation
     */ 
    public function getComparation()
    {
        return $this->comparation;
    }

    /**
     * Get filter logic
     *
     * @return string
     */ 
    public function getFilterLogic()
    {
        return $this->filterLogic;
    }

    /**
     * Set filter logic
     *
     * @param string $filterLogic  Filter logic
     *
     * @return self
     */ 
    public function setFilterLogic($filterLogic)
    {
        $this->filterLogic = $filterLogic;
        return $this;
    }
    
    /**
     * Generate LEFT LIKE wildcard
     *
     * @param string $value
     * @return string
     */
    public static function generateLeftLike($value)
    {
        return "%".$value;
    }
    
    /**
     * Generate CENTER LIKE wildcard
     *
     * @param string $value
     * @return string
     */
    public static function generateCenterLike($value)
    {
        return "%".$value."%";
    }
    
    /**
     * Generate RIGHT LIKE wildcard
     *
     * @param string $value
     * @return string
     */
    public static function generateRightLike($value)
    {
        return $value."%";
    }
    
    public function __toString()
    {
        return json_encode(array(
            'field'=>$this->field,
            'value'=>$this->value,
            'comparation'=>
                array(
                    $this->comparation->getComparison()
                ),
            'filterLogic'=>$this->filterLogic
        ));
    }
}