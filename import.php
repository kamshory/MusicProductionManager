<?php

use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\Utility\SongFileUtil;

require_once "inc/app.php";

function insertSongDraft($database, $path)
{
    $baseName = basename($path);
    $withoutExt = preg_replace('/\.\w+$/', '', $baseName);
    $arr = explode("_", $withoutExt);
    if(count($arr) > 5)
    {

        $timestamp = strtotime($arr[0]."-".$arr[1]."-".$arr[2]." ".$arr[3].":".$arr[4].":".$arr[5]);

        $songDraft = new SongDraft(null, $database);
        try
        {
            $songDraftId = SongFileUtil::generateNewId($timestamp);
            $songDraft->setSongDraftId($songDraftId);
            $query = $songDraft->insertQuery();
            return $query;
        }
        catch(Exception $e)
        {
            // do nothing
        }
    }
}
$files = glob(dirname(__DIR__)."/raw/*.mp3");

foreach($files as $file)
{
    echo insertSongDraft($database, $file)."\r\n\r\n";
}