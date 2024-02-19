<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\GenreDto;
use Pico\Data\Entity\Genre;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->setActive(true);
$genre = new Genre($inputPost, $database);

try
{
    $now = date('Y-m-d H:i:s');
    $genre->setTimeCreate($now);
    $genre->setTimeEdit($now);
    $genre->setIpCreate($_SERVER['REMOTE_ADDR']);
    $genre->setIpEdit($_SERVER['REMOTE_ADDR']);
    $genre->setAdminCreate($currentLoggedInUser->getUserId());
    $genre->setAdminEdit($currentLoggedInUser->getUserId());
    
    $savedData = new Genre(null, $database);
    $saved = $savedData->findOneByName($inputPost->getName());
    if($saved->getGenreId() != "")
    {
        $genre->setGenreId($saved->getGenreId());
    }
    else
    {
        $genre->save();
    }  
}
catch(Exception $e)
{
    $genre->insert();
}
$restResponse = new PicoResponse();
$response = GenreDto::valueOf($genre);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
