<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();
$rating = $inputPost->getRating() * 1;

try
{
    $now = date('Y-m-d H:i:s');
    
    $song1 = new Song(null, $database);
    $song1->findOneBySongId($inputPost->getSongId());
    $song1->setRating($rating);
    $song1->update();
    // save rating
    SongUtil::setRating($database, $song1->getSongId(), $currentLoggedInUser->getUserId(), $rating, $now);

    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Set rating song ".$song->getSongId(), $inputGet, $inputPost);
    
    $allRating = SongUtil::getRating($database, $song1->getSongId());

    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Set rating song ".$song->getSongId(), $inputGet, $inputPost);
    
    $response = new stdClass();
    $response->rating = $allRating;
    $response->song_id = $song1->getSongId();
    $restResponse = new PicoResponse();    
    $restResponse->sendResponseJSON($response, null, PicoHttpStatus::HTTP_OK);

}
catch(Exception $e)
{
    // do nothing
}
