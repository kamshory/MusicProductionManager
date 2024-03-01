<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();
$rating = $inputPost->getRating() * 1;

try
{
    // get album ID begin
    $song1 = new Song(null, $database);
    $song1->findOneBySongId($inputPost->getSongId());
    $song1->setRating($rating);
    $song1->update();
    
    $response = new stdClass();
    $response->rating = $rating;
    $response->song_id = $song1->getSongId();
    $restResponse = new PicoResponse();    
    $restResponse->sendResponseJSON($response, null, PicoHttpStatus::HTTP_OK);

}
catch(Exception $e)
{
    // do nothing
}
