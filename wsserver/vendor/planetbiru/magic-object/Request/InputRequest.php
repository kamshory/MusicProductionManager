<?php

namespace MagicObject\Request;

class  InputRequest extends PicoRequestTool {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_REQUEST);
    }
}