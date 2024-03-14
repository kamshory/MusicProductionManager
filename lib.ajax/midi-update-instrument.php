<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use Midi\MidiInstrument;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

/**
 * Fix value
 *
 * @param mixed $value
 * @return integer
 */
function fixValue($value)
{
    $val = (int) $value;
    if($val <= 0)
    {
        $val = 1;
    }
    return $val;
}

/**
 * Check if request is via AJAX
 *
 * @return bool
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

$inputPost = new InputPost();
$songId = $inputPost->getSongId();
$newInstr = $inputPost->getNewInstrument();

if ($songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

    $midiPath = $song->getFilePathMidi();

	$midi = new MidiInstrument();
	$midi->importMid($midiPath);
    
    $midi->updateMidInstrument(json_decode($newInstr));
    
    $midi->saveMidFile($midiPath, 0777);

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $now = date("Y-m-d H:i:s");
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress($cfg));
    $userActivityId = UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update MIDI instrument ".$song->getSongId(), $inputGet, $inputPost);
    
    if(isAjax())
	{
        echo json_encode(array('ok' => true));
    }
}

