<?php

namespace MagicObject\Request;

class  InputGet extends PicoRequestBase {
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_GET);
    }
}