<?php

use MagicObject\Request\PicoRequest;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
error_log($inputPost);
$channel = $inputPost->getChannel();
$songId = $inputPost->getSongId();
if ($channel != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);
    $song->setMidiVocalChannel($channel);
    $song->update();

	if(!isset($inputGet))
    {
      	$inputGet = new PicoRequest(INPUT_GET);
    }
	UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update MIDI lyric channel ".$song->getSongId(), $inputGet, $inputPost);

	echo json_encode(array('ok' => true));
}
