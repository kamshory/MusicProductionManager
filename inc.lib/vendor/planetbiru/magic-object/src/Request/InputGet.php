<?php

namespace MagicObject\Request;

class  InputGet extends PicoRequestBase {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_GET);
    }
}