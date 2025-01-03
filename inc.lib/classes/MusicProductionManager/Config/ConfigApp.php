<?php
namespace MusicProductionManager\Config;

use MagicObject\MagicObject;
use MagicObject\SecretObject;

class ConfigApp extends SecretObject
{
    /**
     * Constructor
     *
     * @param mixed $data Initial data
     * @param boolean $readonly Readonly flag
     */
    public function __construct($data = null, $readonly = false)
    {
        if($data != null)
        {
            parent::__construct($data);
        }
        $this->readOnly($readonly);
    }
    
}