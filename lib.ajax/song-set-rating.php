<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$rating = $inputPost->getRating() * 2;

try
{
    // get album ID begin
    $song1 = new Song(null, $database);
    $song1->findOneBySongId($inputPost->getSongId());
    $song1->setRating($rating);
    $song1->update();
    
    $response = new stdClass();
    $response->rating = $rating / 2;
    $response->song_id = $song1->getSongId();
    $restResponse = new PicoResponse();    
    $restResponse->sendResponseJSON($response, null, PicoHttpStatus::HTTP_OK);

}
catch(Exception $e)
{
    // do nothing
}
