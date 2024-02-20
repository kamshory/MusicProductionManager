<?php

namespace MagicObject\Response\Generated;

use Exception;
use MagicObject\Database\PicoSortable;
use MagicObject\MagicObject;

class PicoSelectOption
{
    /**
     * Object
     *
     * @var MagicObject
     */
    private $object;

    /**
     * Map
     *
     * @var array
     */
    private $map = array();

    /**
     * Value
     *
     * @var mixed
     */
    private $value;
    
    /**
     * Attributes
     *
     * @var array
     */
    private $attributes = array();
    
    /**
     * Rows
     *
     * @var array
     */
    private $rows = array();
    
    /**
     * Sortable
     *
     * @var PicoSortable
     */
    private $sortable = null;

    /**
     * Constructor
     *
     * @param MagicObject $object
     * @param array $map
     * @param mixed $value
     * @param array|null $attributes
     * @param PicoSortable $sortable
     */
    public function __construct($object, $map, $value, $attributes = null, $sortable = null)
    {
        $this->object = $object;
        $this->map = $map;
        $this->value = $value;
        if(isset($attributes) && is_array($attributes))
        {
            $this->attributes = $attributes;
        }
        if($sortable != null)
        {
            $this->sortable = $sortable;
        }
        else
        {
            $this->sortable = new PicoSortable('name', PicoSortable::ORDER_TYPE_DESC);
        }
        $this->findAllActive();
    }

    /**
     * Create attributes
     *
     * @param MagicObject $row
     * @param string $attr
     * @param string $value
     * @return array
     */
    private function createAttributes($row, $attr, $value)
    {
        $optAttributes = array();
        if(is_array($this->attributes))
        {
            foreach($this->attributes as $k=>$v)
            {
                $val = $row->get($v);
                if($val != null)
                {
                    $optAttributes[$k] = $val;
                }
            }
        }
        if($value == $this->value)
        {
            $optAttributes['selected'] = 'selected';
        }
        $optAttributes[$attr] = $value;
        return $optAttributes;
    }

    /**
     * Find all data from database
     *
     * @return void
     */
    private function findAllActive()
    {
        try
        {         
            $result = $this->object->findByActive(true, null, $this->sortable);
            foreach($result->getResult() as $row)
            {
                $value = $row->get($this->map['value']);
                $label = $row->get($this->map['label']);
                $optAttributes = $this->createAttributes($row, 'value', $value);
                $this->rows[] = array(
                    'attribute'=>$optAttributes,
                    'textNode'=>$label
                );
            }
        }
        catch(Exception $e)
        {
            // do nothing
        }
    }

    /**
     * Convert associated array to HTML attributes as string
     *
     * @param array $array
     * @return string
     */
    private function attributeToString($array)
    {
        if($array == null || empty($array))
        {
            return "";
        }
        $optAttributes = array();
        foreach($array as $key=>$value)
        {
            $optAttributes[] = $key."=\"".htmlspecialchars($value)."\"";
        }
        return rtrim(" ".implode(" ", $optAttributes));
    }

    public function __toString()
    {
        $texts = array();
        foreach($this->rows as $row)
        {
            $optAttributes = $this->attributeToString($row['attribute']);
            $texts[] = "<option".$optAttributes.">".htmlspecialchars($row['textNode'])."</option>";
        }
        return implode("\r\n", $texts);
    }
}