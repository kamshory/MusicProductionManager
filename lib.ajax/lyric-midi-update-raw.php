<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new InputPost();
$lyricMidiRaw = $inputPost->getRaw();
$songId = $inputPost->getSongId();
if ($lyricMidiRaw != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);
    $song->setLyricMidiRaw($lyricMidiRaw);
    $song->update();

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update raw MIDI lyric ".$song->getSongId(), $inputGet, $inputPost);
}
