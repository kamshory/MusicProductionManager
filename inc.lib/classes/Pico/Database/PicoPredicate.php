<?php

namespace Pico\Database;

class PicoPredicate
{
    /**
     * @var string
     */
    private $filed = "";

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
     * Equals
     * @param string $field
     * @param mixed $value
     */
    public function equals($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::equals($value);
    }

    /**
     * Not equals
     * @param string $field
     * @param mixed $value
     */
    public function notEquals($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::NOT_EQUALS);
    }

    /**
     * Like
     * @param string $field
     * @param mixed $value
     */
    public function like($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::like($value);
    }

    /**
     * Not like
     * @param string $field
     * @param mixed $value
     */
    public function notLike($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = PicoDataComparation::notLike($value);
    }

    /**
     * Less than
     * @param string $field
     * @param mixed $value
     */
    public function lessThan($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::LESS_THAN);
    }

    /**
     * Greater than
     * @param string $field
     * @param mixed $value
     */
    public function greaterThan($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::GREATER_THAN);
    }

    /**
     * Less than or equals
     * @param string $field
     * @param mixed $value
     */
    public function lessThanOrEquals($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::LESS_THAN_OR_EQUALS);
    }

    /**
     * Greater than or equals
     * @param string $field
     * @param mixed $value
     */
    public function greaterThanOrEquals($field, $value)
    {
        $this->filed = $field;
        $this->value = $value;
        $this->comparation = new PicoDataComparation($value, PicoDataComparation::GREATER_THAN_OR_EQUALS);
    }

    /**
     * Get the value of filed
     *
     * @return  string
     */ 
    public function getFiled()
    {
        return $this->filed;
    }

    /**
     * Get value
     *
     * @return  mixed
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get data comparation
     *
     * @return  PicoDataComparation
     */ 
    public function getComparation()
    {
        return $this->comparation;
    }

    /**
     * Get filter logic
     *
     * @return  string
     */ 
    public function getFilterLogic()
    {
        return $this->filterLogic;
    }

    /**
     * Set filter logic
     *
     * @param  string  $filterLogic  Filter logic
     *
     * @return  self
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
}