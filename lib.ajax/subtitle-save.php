<?php

use Pico\Data\Entity\Song;

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
}
catch(Exception $e)
{
   // do nothing
}