<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\File\PicoUploadFileItem;
use MagicObject\File\PicoUplodFile;
use MagicObject\MagicObject;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\File\FileMp3;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongFileUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$uploadedFile = new PicoUplodFile();

/**
 * Process file
 *
 * @param MagicObject $cfg
 * @param PicoDatabase $database
 * @param PicoUploadFileItem $file
 * @param EntityUser $currentLoggedInUser
 * @return void
 */
function processFile($cfg, $database, $file, $currentLoggedInUser)
{
    $name = $file->getName();
    $string = preg_replace('/[\D]/', ' ', $name);
    $string = preg_replace('/\s\s+/', ' ', $string);
    $arr = explode(' ', $string);
    $datetime = sprintf("%s-%s-%s %s:%s:%s", $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5]);
    $timestamp = strtotime($datetime);
    $id = SongFileUtil::generateNewId($timestamp);
    $fileName = $id;
    
    $defaultTargetDir = dirname(__DIR__)."/files/$id";

    if(stripos($defaultTargetDir, ":") !== false)
    {
        $arr1 = explode(":", $defaultTargetDir, 2);
        $defaultTargetDir = end($arr1);
        $defaultTargetDir = SongFileUtil::fixDirectorySeparator($defaultTargetDir);
    }

    $targetDir = SongFileUtil::getSongDraftBasePath($cfg, $id, $defaultTargetDir);  
    SongFileUtil::prepareDir($targetDir);
    $now = date("Y-m-d H:i:s", $timestamp);
    $ip = ServerUtil::getRemoteAddress($cfg);  
    $randomId = $timestamp;
    $songDraft = new SongDraft(null, $database);

    try
    {
        $songDraft->findOneByRandomId($randomId);
    }
    catch(Exception $e)
    {
        $songDraft = new SongDraft(null, $database);
        $path = $targetDir."/".$fileName.".mp3";        
        $file->moveTo($path);
        $songDraft->setName($now);
        $songDraft->setFileSize(filesize($path));
        $songDraft->setSongDraftId($id);
        $songDraft->setSha1File(sha1_file($path));
        $songDraft->setRandomId($randomId);
        $songDraft->setFilePath($path);
        $songDraft->setAdminCreate($currentLoggedInUser->getUserId());
        $songDraft->setAdminEdit($currentLoggedInUser->getUserId());
        $songDraft->setTimeCreate($now);
        $songDraft->setTimeEdit($now);
        $songDraft->setIpCreate($ip);
        $songDraft->setIpEdit($ip);

        if($currentLoggedInUser->issetArtist())
        {
            // set artist ID if exists
            $songDraft->setArtistId($currentLoggedInUser->getArtist()->getArtistId());
        }

        // get MP3 duration
        $mp3file = new FileMp3($path); 
        $duration = $mp3file->getDuration(); 
        $songDraft->setDuration($duration);
        $songDraft->insert();
    }
}

if(isset($uploadedFile->file))
{
    $uploadedFiles = $uploadedFile->file->getAll();
    foreach($uploadedFiles as $file)
    {
        processFile($cfg, $database, $file, $currentLoggedInUser);       
    }
}