<?php

use Pico\Data\Entity\Song;
use Pico\Request\PicoRequest;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$songId = $inputPost->getSongId();

if(empty($songId))
{
    exit();
}
$song = new Song($inputPost, $database);
try
{
    $song->update();
}
catch(Exception $e)
{
   // do nothing
}