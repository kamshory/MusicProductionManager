<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Exceptions\NoRecordFoundException;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$songId = $database->generateNewId();

$inputPost = new InputPost();
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
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
    // get producer
    $producerId = "";
    try
    {
        $album = new Album(null, $database);
        $album->findOneByAlbumId($inputPost->getAlbumId());
        $producerId = $album->getProducerId();
    }
    catch(Exception $e)
    {
        // do nothing
    }
    $song->setProducerId($producerId);
    
    $now = date('Y-m-d H:i:s');
    $song->setTimeCreate($now);
    $song->setIpCreate(ServerUtil::getRemoteAddress());
    $song->setAdminCreate($currentLoggedInUser->getUserId());

    $song->setTimeEdit($now);
    $song->setIpEdit(ServerUtil::getRemoteAddress());
    $song->setAdminEdit($currentLoggedInUser->getUserId());

    $song->save();

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "create", $now, ServerUtil::getRemoteAddress());
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Add song ".$song->getSongId(), $inputGet, $inputPost);

    $restResponse = new PicoResponse();    
    $queryBuilder = new PicoDatabaseQueryBuilder($database);
    $sql = $queryBuilder->newQuery()
        ->select("song.*, 
        (select artist.name from artist where artist.artist_id = song.artist_vocalist limit 0,1) as artist_vocal_name,
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
