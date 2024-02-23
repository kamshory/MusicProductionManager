<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$lyricMidiRaw = $inputPost->getRaw();
$songId = $inputPost->getSongId();
if ($lyricMidiRaw != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);
    $song->setLyricMidiRaw($lyricMidiRaw);
    $songUpdate->update();

    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update raw MIDI lyric ".$song->getSongId());
}
