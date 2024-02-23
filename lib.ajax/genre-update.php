<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\GenreDto;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Utility\UserUtil;

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

    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update genre ".$genre->getGenreId());

    $restResponse = new PicoResponse();
    $response = GenreDto::valueOf($genre);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
