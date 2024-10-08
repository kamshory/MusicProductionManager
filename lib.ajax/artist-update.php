<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\ArtistDto;
use MusicProductionManager\Data\Entity\Artist;
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
$inputPost->filterStageName(FILTER_SANITIZE_SPECIAL_CHARS);
$artist = new Artist($inputPost, $database);

try
{
    $now = date('Y-m-d H:i:s');
    $artist->setTimeCreate($now);
    $artist->setTimeEdit($now);
    $artist->setIpCreate(ServerUtil::getRemoteAddress($cfg));
    $artist->setIpEdit(ServerUtil::getRemoteAddress($cfg));
    $artist->setAdminCreate($currentLoggedInUser->getUserId());
    $artist->setAdminEdit($currentLoggedInUser->getUserId());
    
    $artist->update();

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Update artist ".$artist->getArtistId(), $inputGet, $inputPost);

    $restResponse = new PicoResponse();
    $response = ArtistDto::valueOf($artist);
    $restResponse->sendResponse($response, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
