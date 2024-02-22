<?php

use MagicObject\Request\PicoRequest;
use Midi\MidiScale;
use MusicProductionManager\Data\Entity\Song;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$songId = $inputPost->getSongId();
$numerator = $inputPost->getNumerator();
$denominator = $inputPost->getDenominator();
if ($lyric != null && $songId != null) {

	$song = new Song(null, $database);
	$song->findOneBySongId($songId);

    $midiPath = $song->getFilePathMidi();

	$midi = new MidiScale();
	$midi->importMid($midiPath);
    
    $rescaled = $midi->rescale($numerator, $denominator);
    $rescaled->saveMidFile($midiPath, 0777);
    
	echo json_encode(array('ok' => true));
}
