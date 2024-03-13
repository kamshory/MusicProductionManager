<?php

use MagicObject\Request\InputGet;
use Midi\MidiLyric;
use MusicProductionManager\Utility\SongUtil;

require_once "inc/app.php";

$inputGet = new InputGet();

echo SongUtil::getRating($database, $inputGet->getSongId());
