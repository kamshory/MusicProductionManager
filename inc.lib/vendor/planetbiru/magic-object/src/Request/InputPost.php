<?php

namespace MagicObject\Request;

class  InputPost extends PicoRequestBase {
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_POST);
    }
}