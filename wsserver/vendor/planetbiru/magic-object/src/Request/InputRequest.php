<?php

namespace MagicObject\Request;

class  InputRequest extends PicoRequestBase {
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_REQUEST);
    }
}