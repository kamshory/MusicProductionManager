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
    $savedData = new Genre(null, $database);
    $saved = $savedData->findOneByName($inputPost->getName());
    if($saved->getGenreId() != "")
    {
        $genre->setGenreId($saved->getGenreId());
    }
    else
    {
        $data->save();
    }  
}
catch(Exception $e)
{
    $genre->insert();
}
$restResponse = new PicoResponse();
$response = GenreDto::valueOf($genre);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
