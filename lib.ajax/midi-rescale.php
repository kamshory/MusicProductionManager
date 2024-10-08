<?php

use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use Midi\MidiScale;
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
 * @return boolean
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

$inputPost = new InputPost();
$songId = $inputPost->getSongId();

$numerator = 1;
$denominator = 1;

if($inputPost->getScale() != null)
{
    $scale = $inputPost->getScale();
    $arr = explode("/", $scale, 2);
    $numerator = isset($arr[0]) ? (int) $arr[0] : 1;
    $denominator = isset($arr[1]) ? (int) $arr[1] : 1;
}
else
{
    $numerator = $inputPost->getNumerator();
    $denominator = $inputPost->getDenominator();
}

$numerator = fixValue($numerator);
$denominator = fixValue($denominator);

if ($songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

    $midiPath = $song->getFilePathMidi();

	$midi = new MidiScale();
	$midi->importMid($midiPath);
    
    $rescaled = $midi->rescale($numerator, $denominator);
    $rescaled->saveMidFile($midiPath, 0777);
    
    // reload file to get real BPM
    $midi = new MidiScale();
    $midi->importMid($midiPath);


    $song->setBpm($midi->getBpm());

    $ts = $midi->getTimeSignature();
    $timeSignature = 'n/a';
    if (isset($ts) && is_array($ts) && !empty($ts)) {
        $arr0 = $ts[0];
        if(!empty($arr0))
        {
            $ts2 = explode(' ', $arr0[0]['time_signature']);
            $timeSignature = $ts2[0];
        }
    }
    $song->setTimeSignature($timeSignature);

    $song->update();


    
    
    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $now = date("Y-m-d H:i:s");
    $userActivityId = UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Rescale MIDI ".$song->getSongId(), $inputGet, $inputPost);
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress($cfg), $userActivityId);
    
    if(isAjax())
	{
        echo json_encode(array('ok' => true));
    }
}

