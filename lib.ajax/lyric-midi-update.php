<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;


require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$lyric = $inputPost->getLyric();
$songId = $inputPost->getSongId();
if ($lyric != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

	$midiPath = $song->getFilePathMidi();

	$midi = new MidiLyric();
	$midi->importMid($midiPath);
	$midi->addLyric(json_decode($lyric));
	$midi->saveMidFile($midiPath, 0777);
	echo json_encode(array('ok' => true));
}
