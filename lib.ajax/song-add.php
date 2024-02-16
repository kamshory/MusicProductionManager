<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Entity\Song;
use Pico\Database\PicoDatabaseQueryBuilder;
use Pico\Exceptions\NoRecordFoundException;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";
$songId = $database->generateNewId();

$inputPost = new PicoRequest(INPUT_POST);
$inputPost->filterTitle(FILTER_SANITIZE_SPECIAL_CHARS);

$randomSongId = $inputPost->getRandomSongId();

$song = new Song(null, $database);
if(!empty($randomSongId))
{
    $savedData = new Song(null, $database);
    try
    {
        $savedData->findOneByRandomSongId($randomSongId);
        $songId = $savedData->getSongId();
        $song = new Song($inputPost, $database); 
        $song->setSongId($songId);
    }
    catch(NoRecordFoundException $e)
    {
        // do nothing
        $song = new Song($inputPost, $database); 
        $song->setSongId($songId);
        $song->setActive(true);    
    }
}
else
{
    $song = new Song($inputPost, $database);
    $song->setSongId($songId);
    $song->setActive(true);
}

try
{
    $now = date('Y-m-d H:i:s');
    $song->setTimeCreate($now);
    $song->setIpCreate($_SERVER['REMOTE_ADDR']);
    $song->setAdminCreate('1');

    $song->setTimeEdit($now);
    $song->setIpEdit($_SERVER['REMOTE_ADDR']);
    $song->setAdminEdit('1');

    $song->save();

    $restResponse = new PicoResponse();    
    $queryBuilder = new PicoDatabaseQueryBuilder($database);
    $sql = $queryBuilder->newQuery()
        ->select("song.*, 
        (select artist.name from artist where artist.artist_id = song.artist_vocal limit 0,1) as artist_vocal_name,
        (select artist.name from artist where artist.artist_id = song.artist_composer limit 0,1) as artist_composer_name,
        (select artist.name from artist where artist.artist_id = song.artist_arranger limit 0,1) as artist_arranger_name,
        (select genre.name from genre where genre.genre_id = song.genre_id limit 0,1) as genre_name,
        (select album.name from album where album.album_id = album.album_id limit 0,1) as album_name
        ")
        ->from("song")
        ->where("song.song_id = ? ", $song->getSongId())
        ;
        try
        {
            $record = $database->fetch($sql, PDO::FETCH_OBJ);
            $restResponse->sendResponseJSON($record, null, PicoHttpStatus::HTTP_OK);
        }
        catch(Exception $e)
        {
            // do nothing
        }
}
catch(Exception $e)
{
    // do nothing
}
