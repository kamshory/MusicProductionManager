<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\File\FileMp3;
use MusicProductionManager\Utility\SongFileUtil;

require_once "inc/app.php";

/**
 * Insert song draft
 *
 * @param PicoDatabase $database
 * @param string $path
 * @return PicoDatabaseQueryBuilder
 */
function insertSongDraft($database, $path)
{
    $baseName = basename($path);
    $withoutExt = preg_replace('/\.\w+$/', '', $baseName);
    $arr = explode("_", $withoutExt);
    if(count($arr) > 5)
    {

        $timestamp = strtotime($arr[0]."-".$arr[1]."-".$arr[2]." ".$arr[3].":".$arr[4].":".$arr[5]);
        $now = date("Y-m-d H:i:s", $timestamp);
        
        $songDraftId = SongFileUtil::generateNewId($timestamp);
        $filePath = "/var/www/html/studio/production/files/draft/$songDraftId/$songDraftId".".mp3";
        
        $tempMath = __DIR__ . "/files/draft/$songDraftId/$songDraftId".".mp3";
        

        $songDraft = new SongDraft(null, $database);
        $randomId = $timestamp * 1000;
        try
        {
            $songDraft->findOneByRandomId($randomId);

            
            
            $query = $songDraft->saveQuery();
            
            //copy($path, $tempMath);
            
            return $query;
        }
        catch(Exception $e)
        {
            
            $songDraft = new SongDraft(null, $database);
            $songDraft->setSongDraftId($songDraftId);
            $songDraft->setRandomId($randomId);
            $songDraft->setName($now);
            $songDraft->setTimeCreate($now);
            $songDraft->setTimeEdit($now);
            $songDraft->setFileSize(filesize($path));
            $songDraft->setFilePath($filePath);
            $songDraft->setSha1File(sha1_file($path));
            
            // get MP3 duration
            $mp3file = new FileMp3($path); 
            $duration = $mp3file->getDuration(); 
            $songDraft->setDuration($duration);
            $songDraft->setActive(true);

            $query = $songDraft->insertQuery();
            $database->execute($query);
            /*
            if(!file_exists(dirname($tempMath)))
            {
                mkdir(dirname($tempMath), 0755, true);
            }
            copy($path, $tempMath);
            */
            
            return $query;
            
        }
    }
    return new PicoDatabaseQueryBuilder($database);
}
$files = glob(dirname(__DIR__)."/raw/*.mp3");

foreach($files as $file)
{
    echo insertSongDraft($database, $file)."\r\n\r\n";
}