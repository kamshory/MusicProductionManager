<?php

use MagicObject\Request\PicoRequest;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

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
    if(!isset($inputGet))
    {
        $inputGet = new PicoRequest(INPUT_GET);
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update lyric ".$song->getSongId(), $inputGet, $inputPost);
}
catch(Exception $e)
{
   // do nothing
}