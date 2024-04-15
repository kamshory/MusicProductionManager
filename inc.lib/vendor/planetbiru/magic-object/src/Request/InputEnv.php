<?php

namespace MagicObject\Request;

class  InputEnv extends PicoRequestBase {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_ENV);
    }
}