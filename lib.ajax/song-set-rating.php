<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();
$inputGet = new InputGet();
$rating = $inputPost->getRating() * 1;

try
{
    $now = date('Y-m-d H:i:s');
    
    // save rating
    SongUtil::setRating($database, $inputPost->getSongId(), $currentLoggedInUser->getUserId(), $rating, $now);
    
    $allRating = SongUtil::getRating($database, $inputPost->getSongId());
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Set rating song ".$inputPost->getSongId(), $inputGet, $inputPost);

    $song1 = new Song(null, $database);
    $song1->findOneBySongId($inputPost->getSongId());
    $song1->setRating($rating);
    // update average to song
    $song1->update();
    
    $response = new stdClass();
    $response->rating = $allRating;
    $response->song_id = $inputPost->getSongId();
    $restResponse = new PicoResponse();    
    $restResponse->sendResponseJSON($response, null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
