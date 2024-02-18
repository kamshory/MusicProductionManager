<?php
use Pico\Data\Entity\EntitySong;
use Pico\Request\PicoRequest;

require_once "inc/auth-with-login-form.php";

$inputGet = new PicoRequest(INPUT_GET);
if($inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());

    if($inputGet->equalsType('mp3') && file_exists($song->getFilePath()))
    {
        $filename = $song->getTitle().".mp3";
        header("Content-type: audio/mp3");
        readfile($song->getFilePathMidi());
    }
    if($inputGet->equalsType('midi') && file_exists($song->getFilePathMidi()))
    {
        $filename = $song->getTitle().".mid";
        header("Content-type: audio/midi");
        readfile($song->getFilePathMidi());
    }
    else if($inputGet->equalsType('pdf') && file_exists($song->getFilePathPdf()))
    {
        $filename = $song->getTitle().".pdf";
        header("Content-type: application/pdf");
        readfile($song->getFilePathMidi());
    }
    else if($inputGet->equalsType('xml') && file_exists($song->getFilePathXml()))
    {
        $filename = $song->getTitle().".xml";
        header("Content-type: application/xml");
        readfile($song->getFilePathMidi());
    }
  }
  catch(Exception $e)
  {
    // do nothing
  }
  exit();
}
