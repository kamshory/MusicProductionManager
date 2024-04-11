<?php

namespace MagicObject\Request;

class  InputRequest extends PicoRequestBase {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_REQUEST);
    }
}