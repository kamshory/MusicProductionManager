<?php

namespace MagicObject\DataLabel;

use MagicObject\SetterGetter;
use MagicObject\Util\PicoAnnotationParser;
use stdClass;

class DataLabel 
{
    /**
     * Data
     *
     * @var DataLabel[]
     */
    private $data = array();
    
    /**
     * Append data
     *
     * @param DataLabel $data
     * @return self
     */
    public function append($data)
    {
        $this->data[] = $data;
    }
    
    public function generate()
    {
        foreach($this->data as $data)
        {
            
        }
    }
}