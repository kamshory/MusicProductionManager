<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new InputPost();
$channel = $inputPost->getChannel();
$songId = $inputPost->getSongId();
if ($channel != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);
    $song->setMidiVocalChannel($channel);
    $song->update();

	if(!isset($inputGet))
    {
		$inputGet = new InputGet();
    }
	$now = date("Y-m-d H:i:s");
	$userActivityId = UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update MIDI lyric channel ".$song->getSongId(), $inputGet, $inputPost);
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress($cfg), $userActivityId);

	echo json_encode(array('ok' => true));
}
