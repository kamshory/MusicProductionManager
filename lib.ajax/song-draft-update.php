<?php

use MagicObject\Constants\PicoHttpStatus;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Song;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MagicObject\Util\Dms;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";


$inputPost = new InputPost();
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxActive(false);
$inputPost->checkboxVocal(false);

try
{
    // get album ID begin
    $now = date('Y-m-d H:i:s');
    $song1 = new SongDraft(null, $database);
    $song1->findOneBySongDraftId($inputPost->getSongDraftId());
    
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
