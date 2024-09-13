<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MagicObject\Util\Dms;
use MusicProductionManager\Data\Entity\SongDraft;

require_once dirname(__DIR__)."/inc/auth.php";


$inputPost = new InputPost();
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterLyric(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxVocal(false);

try
{
    // get album ID begin
    $now = date('Y-m-d H:i:s');
    $songDraft = new SongDraft(null, $database);
    $songDraft->findOneBySongDraftId($inputPost->getSongDraftId());

    $songDraft->setName($inputPost->getName());
    $songDraft->setTitle($inputPost->getTitle());
    $songDraft->setLyric($inputPost->getLyric());
    $songDraft->setActive($inputPost->getActive());
    $songDraft->update();
    
    $restResponse = new PicoResponse();    
    $query = new PicoDatabaseQueryBuilder($database);
    $sql = $query->newQuery()
        ->select("*")
        ->from("song_draft")
        ->where("song_draft_id = ? ", $inputPost->getSongDraftId());

    $record = $database->fetch($sql, PDO::FETCH_OBJ);
    $record->duration = (new Dms())->ddToDms($record->duration / 3600)->printDms(true, true);
    $restResponse->sendResponseJSON($record, null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
