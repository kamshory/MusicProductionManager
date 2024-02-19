<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\GenreDto;
use Pico\Data\Entity\Genre;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxActive(false);

$genreId = $inputPost->getGenreId();
$name = $inputPost->getName();
if(empty($genreId) || empty($name))
{
    exit();
}

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
    
    $genre->update();
    $restResponse = new PicoResponse();
    $response = GenreDto::valueOf($genre);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
