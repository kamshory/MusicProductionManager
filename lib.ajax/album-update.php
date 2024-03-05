<?php

use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";

$inputPost = new InputPost();
$inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterDescription(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxActive(false);
$inputPost->checkboxAsDraft(false);
$album = new Album($inputPost, $database);

try {
    $song = new Song(null, $database);
    
    // get producer
    $producerId = "";
    try
    {
        $album->findOneByAlbumId($inputPost->getAlbumId);
        $producerId = $album->getProducerId();
    }
    catch(Exception $e)
    {
        $album = new Album($inputPost, $database);
    }

    try {
        $result = $song->findByAlbumId($album->getAlbumId());
        $numberOfSong = 0;
        $totalDuration = 0;
        foreach ($result->getResult() as $record) {
            $totalDuration += $record->getDuration();
            $numberOfSong++;
            
            // save producer
            $record->setProducerId($producerId);
            $record->save();
        }
        $album->setDuration($totalDuration);
        $album->setNumberOfSong($numberOfSong);
    } catch (Exception $e) {
        $album->setDuration(0);
        $album->setNumberOfSong(0);
    }

    $now = date('Y-m-d H:i:s');
    $album->setTimeEdit($now);
    $album->setIpEdit($_SERVER['REMOTE_ADDR']);
    $album->setAdminEdit($currentLoggedInUser->getUserId());

    $album->update();

    if (!isset($inputGet)) {
        $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update album " . $album->getAlbumId(), $inputGet, $inputPost);

    $restResponse = new PicoResponse();
    $restResponse->sendResponse($album, 'json', null, PicoHttpStatus::HTTP_OK);
} catch (Exception $e) {
    // do nothing
}
