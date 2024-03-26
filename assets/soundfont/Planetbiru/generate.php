<?php

$directory = "";

if($argc > 1)
{
    $directory = trim($argv[1]);
}
else
{
    if(isset($_GET['directory']))
    {
        $directory = trim($_GET['directory']);
    }
}
$name = $directory;
$directory .= "-mp3";
$path = __DIR__."/$directory.js";
$list = glob(__DIR__."/$directory/*.mp3");
$fp = fopen($path, "w");

fwrite($fp, "if (typeof(MIDI) === 'undefined') var MIDI = {};
if (typeof(MIDI.Soundfont) === 'undefined') MIDI.Soundfont = {};
MIDI.Soundfont.$name = {\r\n");

foreach($list as $file)
{
    $baseName = basename($file);
    if(stripos($baseName, ".") !== false)
    {
        $arr = explode(".", $baseName);
        $baseName = $arr[0];
    }
    fwrite($fp, "\"$baseName\":\"data:audio/mp3;base64,".base64_encode(file_get_contents($file))."\",\r\n");
}
fwrite($fp, "}\r\n");