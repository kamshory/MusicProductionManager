<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$lyric = $inputPost->getLyric();
$midiLyric = $inputPost->getMidiLyric();
$songId = $inputPost->getSongId();
if ($lyric != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

	$song->setLyricMidi($lyric);
	$song->update();

	$midiPath = $song->getFilePathMidi();

	$midi = new MidiLyric();
	$midi->importMid($midiPath);
	$midi->addLyric(json_decode($midiLyric));
	$midi->saveMidFile($midiPath, 0777);


	UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update MIDI lyric ".$song->getSongId());

	echo json_encode(array('ok' => true));
}
