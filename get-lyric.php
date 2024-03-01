<?php

use MagicObject\Request\InputGet;
use Midi\MidiLyric;

require_once "inc/app.php";

$midiLyric = new MidiLyric();
$midiLyric->importMid('test.mid');
echo json_encode($midiLyric->getSong(4), JSON_PRETTY_PRINT);
