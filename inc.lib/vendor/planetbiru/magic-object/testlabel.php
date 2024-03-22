<?php

use MagicObject\File\PicoUplodFile;

require_once "vendor/autoload.php";

$files = new PicoUplodFile();
$file1 = $files->get('test');

// or 
// $file1 = $files->test;

foreach($file1->getAll() as $fileItem)
{
    $temporaryName = $fileItem->getTmpName();
    $name = $fileItem->getName();
    $size = $fileItem->getSize();
    echo "$name | $temporaryName\r\n";
}

