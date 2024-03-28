<?php

namespace MagicObject\Request;

class PicoRequest extends PicoRequestTool
{
    const ACTION_DETAIL = "detail";
    const ACTION_EDIT = "edit";
    const ACTION_ADD = "add";
    
    public function __construct($inputType = INPUT_GET)
    {
        parent::__construct();
        if($inputType == INPUT_GET && isset($_GET))
        {
            $this->loadData($_GET);
        }
        else if($inputType == INPUT_POST && isset($_POST))
        {
            $this->loadData($_POST);
        }
        else if($inputType == INPUT_COOKIE && isset($_COOKIE))
        {
            $this->loadData($_COOKIE);
        }
    }
        
}