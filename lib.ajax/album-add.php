<?php

use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Dto\AlbumDto;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new InputPost();
$inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterDescription(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxActive(false);
$inputPost->checkboxAsDraft(true);
$album = new Album($inputPost, $database);
try
{
    $saved = $album->findOneByName($inputPost->getName());
    if($saved && $saved->hasValueAlbumId())
    {
        $album->setAlbumId($saved->getAlbumId());
    }
    else
    {
        $album->save();
    }
    $restResponse = new PicoResponse();   
    $response = AlbumDto::valueOf($album);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // prevent data corrupt
    $album = new Album($inputPost, $database);
    
    $album->setNumberOfSong(0);
    $album->setDuration(0);
    
    $now = date('Y-m-d H:i:s');
    $album->setTimeCreate($now);
    $album->setTimeEdit($now);
    $album->setIpCreate(ServerUtil::getRemoteAddress($cfg));
    $album->setIpEdit(ServerUtil::getRemoteAddress($cfg));
    $album->setAdminCreate($currentLoggedInUser->getUserId());
    $album->setAdminEdit($currentLoggedInUser->getUserId());
    
    $album->insert();

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Add album ".$album->getAlbumId(), $inputGet, $inputPost);
    
    $restResponse = new PicoResponse();   
    $response = AlbumDto::valueOf($album);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
