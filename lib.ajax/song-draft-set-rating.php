<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();
$inputGet = new InputGet();
$rating = $inputPost->getRating() * 1;
$allRating = $rating;
try
{

    // save rating
    SongUtil::setDraftRating($database, $inputPost->getSongDraftId(), $currentLoggedInUser->getUserId(), $rating, $now);

    $allRating = SongUtil::getDraftRating($database, $inputPost->getSongDraftId());

    $song1 = new SongDraft(null, $database);
    $song1->findOneBySongDraftId($inputPost->getSongDraftId());
    $song1->setRating($allRating);
    // update average to song
    $song1->update();

    UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Give rating song draft ".$inputPost->getSongDraftId(), $inputGet, $inputPost);
    
}
catch(Exception $e)
{
    // do nothing
}
$data = new stdClass;
$data->song_draft_id = $songDraft->getSongDraftId();
$data->rating = $allRating;
$result = json_encode($data);
$restResponse = new PicoResponse();
$restResponse->sendResponse($result, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
