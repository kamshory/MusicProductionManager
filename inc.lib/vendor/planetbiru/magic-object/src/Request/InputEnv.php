<?php

namespace MagicObject\Request;

class  InputEnv extends PicoRequestBase {
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_ENV);
    }
}