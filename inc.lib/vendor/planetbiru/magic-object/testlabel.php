<?php

use MagicObject\File\PicoUplodFile;
use MagicObject\Session\PicoSession;

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


$systemSession = PicoSession::getInstance("TEST");
$systemSession->startSession();
if(!isset($systemSession->coba))
{
    $systemSession->coba = 0;
}
$systemSession->coba++;

echo $systemSession->coba;