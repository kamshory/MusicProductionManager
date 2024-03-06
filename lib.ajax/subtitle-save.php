<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new InputPost();
$songId = $inputPost->getSongId();
$subtitle = $inputPost->getSubtitle();
$duration = $inputPost->getDuration();

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
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Save subtitle ".$song->getSongId(), $inputGet, $inputPost);
}
catch(Exception $e)
{
   // do nothing
}