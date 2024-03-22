<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Request\InputGet;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Entity\SongDraft;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new InputGet();
$songDraft = new SongDraft(null, $database);
try
{
    $songDraft->findOneBySongDraftId($inputGet->getSongDraftId());
    if(!$songDraft->hasValueRating())
    {
        $songDraft->setRating(0);
    }
}
catch(Exception $e)
{
    $songDraft = new SongDraft(null, $database);
    $songDraft->setSongDraftId($inputGet->getSongDraftId());
    $songDraft->setRating(0);
}
$data = new stdClass;
$data->song_draft_id = $songDraft->getSongDraftId();
$data->rating = $songDraft->getRating();
$result = json_encode($data);
$restResponse = new PicoResponse();
$restResponse->sendResponse($result, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
