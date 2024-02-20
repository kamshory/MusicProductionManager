<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;


require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$lyricMidiRaw = $inputPost->getRaw();
$songId = $inputPost->getSongId();
if ($lyricMidiRaw != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);
    $song->setLyricMidiRaw($lyricMidiRaw);
    $songUpdate->update();
}
