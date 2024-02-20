<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\ArtistDto;
use MusicProductionManager\Data\Entity\Artist;


require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->filterStageName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$artist = new Artist($inputPost, $database);

try
{
    $now = date('Y-m-d H:i:s');
    $artist->setTimeCreate($now);
    $artist->setTimeEdit($now);
    $artist->setIpCreate($_SERVER['REMOTE_ADDR']);
    $artist->setIpEdit($_SERVER['REMOTE_ADDR']);
    $artist->setAdminCreate($currentLoggedInUser->getUserId());
    $artist->setAdminEdit($currentLoggedInUser->getUserId());
    
    $artist->save();
    $restResponse = new PicoResponse();
    $response = ArtistDto::valueOf($artist);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
