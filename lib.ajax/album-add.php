<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\AlbumDto;
use Pico\Data\Entity\Album;
use Pico\Request\PicoFilterConstant;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new PicoRequest(INPUT_POST);
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
    $album->setIpCreate($_SERVER['REMOTE_ADDR']);
    $album->setIpEdit($_SERVER['REMOTE_ADDR']);
    $album->setAdminCreate($currentLoggedInUser->getUserId());
    $album->setAdminEdit($currentLoggedInUser->getUserId());
    
    $album->insert();
    
    $restResponse = new PicoResponse();   
    $response = AlbumDto::valueOf($album);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
