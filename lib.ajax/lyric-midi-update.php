<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new InputPost();
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
	
	

	if(!isset($inputGet))
    {
		$inputGet = new InputGet();
    }
	$now = date("Y-m-d H:i:s");
	SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress());
	UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update MIDI lyric ".$song->getSongId(), $inputGet, $inputPost);

	echo json_encode(array('ok' => true));
}
