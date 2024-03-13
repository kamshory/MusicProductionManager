<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use Midi\Midi;
use MusicProductionManager\Data\Entity\Song;
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
$tn = $inputPost->getTrackNumber();
$cn = $inputPost->getChannelNumber();
$dn = (int) ($inputPost->getSemitone());

if ($songId != null && $tn != null && $cn != null && $dn != 0) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

    $midiPath = $song->getFilePathMidi();

	$midi = new Midi();
	$midi->importMid($midiPath);
    

    $trackNumber = $tn == 'all' ? null : (int) $tn;
    $channelNumber = $cn == 'all' ? null : (int) $cn;

    $midi->transposeTrackChannel($trackNumber, $channelNumber, $dn);
    $midi->saveMidFile($midiPath, 0777);

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $now = date("Y-m-d H:i:s");
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "create", $now, $_SERVER['REMOTE_ADDR']);
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Transpose MIDI ".$song->getSongId(), $inputGet, $inputPost);
    
    if(isAjax())
	{
        echo json_encode(array('ok' => true));
    }
}

