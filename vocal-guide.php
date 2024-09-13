<?php

use Midi\MidiLyric;
use MusicProductionManager\Data\Entity\Song;

require_once "inc/app.php";

$songEnt = new Song(null, $database);
try
{
    $songs = $songEnt->findAll(null, null, null);
    $results = $songs->getResult();
    foreach($results as $song)
    {     
        $midiPath = $song->getFilePathMidi();
        if(file_exists($midiPath))
        {
            $midi = new MidiLyric();
            $midi->importMid($midiPath);            
            $vocalGuide = json_encode(array_values($midi->getSong($song->getMidiVocalChannel())));
            $song->setVocalGuide($vocalGuide);
            $song->update();
            echo "Update vocal guide ".$song->getName()."<br>\r\n";
        }
    }
}
catch(Exception $e)
{
    // do nothing
}
