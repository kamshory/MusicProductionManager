<?php

namespace MagicObject\Request;

class  InputPost extends PicoRequestBase {
    public function __construct()
    {
        parent::__construct();
        $this->loadData($_POST);
    }
}