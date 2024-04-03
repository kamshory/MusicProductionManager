<?php

namespace MagicObject\Request;

class  InputGet extends PicoRequestTool {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_GET);
    }
}