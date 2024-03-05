<?php

use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$song_id = trim(@$_POST['song_id']);
$lyric = @$_POST['lyric'];
$duration = trim(@$_POST['duration']);

if(empty($song_id))
{
    exit();
}

$song = new Song(null, $database);
$song->setSongId($song_id);
$song->setLyric($lyric);

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