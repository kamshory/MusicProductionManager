<?php

namespace MagicObject\File;

class PicoUploadFileItem
{
    private $value = array();
    public function __construct($file)
    {
        $this->value = $file;
    }
    
    /**
     * Get temporary name
     *
     * @return string
     */
    public function getTmpName()
    {
        return !isset($this->value['tmp_name']) ? null : $this->value['tmp_name'];
    }
    
    /**
     * Get file name
     *
     * @return string
     */
    public function getName()
    {
        return !isset($this->value['name']) ? null : $this->value['name'];
    }
    
    /**
     * Get error
     *
     * @return mixed
     */
    public function getError()
    {
        return !isset($this->value['error']) ? null : $this->value['error'];
    }
    
    /**
     * Get file size
     *
     * @return integer
     */
    public function getSize()
    {
        return !isset($this->value['size']) ? 0 : $this->value['size'];
    }
    
    /**
     * Get file type
     *
     * @return string
     */
    public function getType()
    {
        return !isset($this->value['type']) ? null : $this->value['type'];
    }
}