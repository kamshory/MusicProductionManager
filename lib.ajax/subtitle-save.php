<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new InputPost();
$songId = $inputPost->getSongId();
$subtitle = $inputPost->getSubtitle();
$duration = $inputPost->getDuration();

$now = date("Y-m-d H:i:s");

if(empty($songId))
{
    exit();
}

$song = new Song(null, $database);
$song->setSongId($songId);
$song->setSubtitle($subtitle);

if($duration != '')
{
    $song->setDuration($duration * 1);
}

try
{
    $song->update();
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress());
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Save subtitle ".$song->getSongId(), $inputGet, $inputPost);
}
catch(Exception $e)
{
   // do nothing
}