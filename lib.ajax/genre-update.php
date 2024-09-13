<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\GenreDto;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();
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
    $genre->setIpCreate(ServerUtil::getRemoteAddress($cfg));
    $genre->setIpEdit(ServerUtil::getRemoteAddress($cfg));
    $genre->setAdminCreate($currentLoggedInUser->getUserId());
    $genre->setAdminEdit($currentLoggedInUser->getUserId());
    
    $genre->update();

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Update genre ".$genre->getGenreId(), $inputGet, $inputPost);

    $restResponse = new PicoResponse();
    $response = GenreDto::valueOf($genre);
    $restResponse->sendResponse($response, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
