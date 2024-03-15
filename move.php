<?php

use MusicProductionManager\Data\Entity\Song;

require_once "inc/app.php";

$songEnt = new Song(null, $database);
try
{
    $songs = $songEnt->findAll(null, null, null);
    $results = $songs->getResult();
    foreach($results as $song)
    {
        $mp3Path = $song->getFilePath();
        $midiPath = $song->getFilePathMidi();
        $xmlPath = $song->getFilePathXml();
        $pdfPath = $song->getFilePathPdf();
        $baseDir = __DIR__ . "/files/" . $song->getSongId();
        if(!file_exists($baseDir))
        {
            mkdir($baseDir, 0755, true);
        }
        if(file_exists($mp3Path))
        {
            $mp3Path2 = $baseDir . "/song.mp3";
            copy($mp3Path, $mp3Path2);
            $song->setFilePath($mp3Path2);
        }
        if(file_exists($midiPath))
        {
            $midiPath2 = $baseDir . "/song.mid";
            copy($midiPath, $midiPath2);
            $song->setFilePathMidi($midiPath2);
        }
        if(file_exists($xmlPath))
        {
            $xmlPath2 = $baseDir . "/song.musicxml";
            copy($xmlPath, $xmlPath2);
            $song->setFilePathXml($xmlPath2);
        }
        if(file_exists($pdfPath))
        {
            $pdfPath2 = $baseDir . "/scores.pdf";
            copy($pdfPath, $pdfPath2);
            $song->setFilePathPdf($pdfPath2);
        }
        $song->update();
    }
}
catch(Exception $e)
{
    
}
