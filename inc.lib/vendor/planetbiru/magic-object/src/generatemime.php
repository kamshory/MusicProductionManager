<?php

use MagicObject\Constants\PicoMimeMap;
use MagicObject\File\PicoUplodFile;

require_once "vendor/autoload.php";


$mimeMap = PicoMimeMap::MIME_TYPES_FOR_EXTENSIONS;
sort($mimeMap);
$arr = array();
foreach($mimeMap as $value)
{
    $key = strtoupper(str_replace(array("/", ".", "-", "+"), "_", $value));
    $arr[] = "const $key = \"".$value."\";\r\n";
}

$arr = array_unique($arr);

file_put_contents("test.txt", implode("", $arr));