<?php

namespace MagicObject\Request;

class  InputPost extends PicoRequestTool {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_POST);
    }
}