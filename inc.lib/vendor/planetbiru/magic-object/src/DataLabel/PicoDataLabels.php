<?php

namespace MagicObject\PicoDataLabel;

use MagicObject\DataLabel\PicoDataLabel;

class PicoDataLabels 
{
    /**
     * Data
     *
     * @var PicoDataLabel[]
     */
    private $data = array();
    
    /**
     * Append data
     *
     * @param PicoDataLabel $data
     * @return self
     */
    public function append($data)
    {
        $this->data[] = $data;
        return $this;
    }
    
    public function generate()
    {
        foreach($this->data as $data)
        {
            
        }
    }
}