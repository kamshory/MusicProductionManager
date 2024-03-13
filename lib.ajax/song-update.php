<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

function updateAlbum($database, $albumId)
{
    if($albumId != null && !empty($albumId)) {
        try
        {
            $song = new Song(null, $database);
            $result = $song->findByAlbumId($albumId);
            $numberOfSong = 0;
            $totalDuration = 0;
            foreach ($result->getResult() as $record)
            {
                $totalDuration += $record->getDuration();
                $numberOfSong++;
            }
            $album = new Album(null, $database);
            $album->setAlbumId($albumId);
            $album->setDuration($totalDuration);
            $album->setNumberOfSong($numberOfSong);
            $album->update();
        }
        catch(Exception $e)
        {
            // do nothing
        }
    }
}

$inputPost = new InputPost();
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxActive(false);
$inputPost->checkboxVocal(false);

try
{
    // get album ID begin
    $now = date('Y-m-d H:i:s');
    $song1 = new Song(null, $database);
    $song1->findOneBySongId($inputPost->getSongId());
    $albumId1 = $song1->getAlbumId();
    $albumId2 = $inputPost->getAlbumId();
    // get album ID end
    
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
    

    $song = new Song($inputPost, $database);
    $song->setTimeEdit($now);
    $song->setProducerId($producerId);
    $song->update();


    // update old album    
    updateAlbum($database, $albumId1);

    // update new album
    if($albumId2 != $albumId1)
    {
        updateAlbum($database, $albumId2);
    }
    
    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $userActivityId = UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update song ".$inputPost->getSongId(), $inputGet, $inputPost);
    SongUtil::updateSong($database, $inputPost->getSongId(), $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress(), $userActivityId);

    $restResponse = new PicoResponse();    
    $queryBuilder = new PicoDatabaseQueryBuilder($database);
    
    $sql = $queryBuilder->newQuery()
        ->select("song.*, 
        (select artist.name from artist where artist.artist_id = song.artist_vocalist limit 0,1) as artist_vocal_name,
        (select artist.name from artist where artist.artist_id = song.artist_composer limit 0,1) as artist_composer_name,
        (select artist.name from artist where artist.artist_id = song.artist_arranger limit 0,1) as artist_arranger_name,
        (select genre.name from genre where genre.genre_id = song.genre_id limit 0,1) as genre_name,
        (select album.name from album where album.album_id = song.album_id limit 0,1) as album_name
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
