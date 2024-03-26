<?php

$name = "";

if($argc > 1)
{
    $name = trim($argv[1]);
}
else
{
    if(isset($_GET['name']))
    {
        $name = trim($_GET['name']);
    }
}

$listMp3 = glob(__DIR__."/$name/*.mp3");
$listOgg = glob(__DIR__."/$name/*.ogg");

if(count($listMp3) > 80)
{
    echo "Generate Soundfont mp3\r\n";
    $directoryMp3 = $name;
    $directoryMp3 .= "-mp3";
    $pathMp3 = __DIR__."/$directoryMp3.js";
    $fpMp3 = fopen($pathMp3, "w");

    fwrite($fpMp3, "if (typeof(MIDI) === 'undefined') var MIDI = {};\r\nif (typeof(MIDI.Soundfont) === 'undefined') MIDI.Soundfont = {};\r\nMIDI.Soundfont.$name = {\r\n");

    foreach($listMp3 as $file)
    {
        $baseName = basename($file);
        if(stripos($baseName, ".") !== false)
        {
            $arr = explode(".", $baseName);
            $baseName = $arr[0];
        }
        fwrite($fpMp3, "\"$baseName\":\"data:audio/mp3;base64,".base64_encode(file_get_contents($file))."\",\r\n");
    }
    fwrite($fpMp3, "}\r\n");
}

if(count($listOgg) > 80)
{
    echo "Generate Soundfont ogg\r\n";
    $directoryOgg = $name;
    $directoryOgg .= "-ogg";
    $pathOgg = __DIR__."/$directoryOgg.js";
    $fpOgg = fopen($pathOgg, "w");

    fwrite($fpOgg, "if (typeof(MIDI) === 'undefined') var MIDI = {};\r\nif (typeof(MIDI.Soundfont) === 'undefined') MIDI.Soundfont = {};\r\nMIDI.Soundfont.$name = {\r\n");

    foreach($listOgg as $file)
    {
        $baseName = basename($file);
        if(stripos($baseName, ".") !== false)
        {
            $arr = explode(".", $baseName);
            $baseName = $arr[0];
        }
        fwrite($fpOgg, "\"$baseName\":\"data:audio/ogg;base64,".base64_encode(file_get_contents($file))."\",\r\n");
    }
    fwrite($fpOgg, "}\r\n");
}