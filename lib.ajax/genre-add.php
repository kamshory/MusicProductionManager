<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\GenreDto;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new InputPost();
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
    $genre->setIpCreate(ServerUtil::getRemoteAddress());
    $genre->setIpEdit(ServerUtil::getRemoteAddress());
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

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Add genre ".$genre->getGenreId(), $inputGet, $inputPost);
}
catch(Exception $e)
{
    $genre->insert();
}
$restResponse = new PicoResponse();
$response = GenreDto::valueOf($genre);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
