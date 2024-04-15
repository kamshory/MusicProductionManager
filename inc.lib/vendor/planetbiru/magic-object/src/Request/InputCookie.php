<?php

namespace MagicObject\Request;

class  InputCookie extends PicoRequestBase {
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_COOKIE);
    }
}