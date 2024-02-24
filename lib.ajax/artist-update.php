<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\ArtistDto;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->filterStageName(FILTER_SANITIZE_SPECIAL_CHARS);
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
    
    $artist->update();

    if(!isset($inputGet))
    {
        $inputGet = new PicoRequest(INPUT_GET);
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update artist ".$artist->getArtistId(), $inputGet, $inputPost);

    $restResponse = new PicoResponse();
    $response = ArtistDto::valueOf($artist);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
