<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
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
    $now = date('Y-m-d H:i:s');
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress());
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update raw MIDI lyric ".$song->getSongId(), $inputGet, $inputPost);
}
