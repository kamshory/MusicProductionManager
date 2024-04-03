<?php

namespace MagicObject;

use MagicObject\Util\PicoAnnotationParser;

class DataTableDetail extends DataTable
{
    public function printData()
    {
        $jsonAnnot = new PicoAnnotationParser(get_class($this));
    }
}