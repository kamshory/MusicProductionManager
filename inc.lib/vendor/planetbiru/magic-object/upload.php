<?php

use MagicObject\File\PicoUplodFile;
use MagicObject\MagicObject;
use MagicObject\Session\PicoSession;

require_once "vendor/autoload.php";

$systemSession = PicoSession::getInstance("TEST");
$systemSession->startSession();

$files = new PicoUplodFile();
$file1 = $files->get('file_uploaded');


// or 
// $file1 = $files->test;

foreach($file1->getAll() as $fileItem)
{
    $temporaryName = $fileItem->getTmpName();
    $name = $fileItem->getName();
    $size = $fileItem->getSize();
    echo "$name | $temporaryName\r\n";
}


if(!isset($systemSession->coba))
{
    $systemSession->coba = 0;
}
$systemSession->coba++;


$magicObject = new MagicObject();

$magicObject->loadYamlString("
data1:
  data2:
    data3:
      data4: apa
      data5: 6666
", true, true, true);

echo $magicObject->getData1()->getData2()->getData3();