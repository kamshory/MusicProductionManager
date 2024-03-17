<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\File\FileMp3;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongFileUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();

if($inputPost->getData() != "")
{
    $timestamp = (int) ($inputPost->getRandomId() / 1000);
    $id = SongFileUtil::generateNewId($timestamp);
    $fileName = $id;

    $defaultTargetDir = dirname(__DIR__)."/files/$id";
    if(stripos($defaultTargetDir, ":") !== false)
    {
        $arr1 = explode(":", $defaultTargetDir, 2);
        $defaultTargetDir = end($arr1);
        $defaultTargetDir = SongFileUtil::fixDirectorySeparator($defaultTargetDir);
    }

    
    $tempDir = dirname(__DIR__)."/temp";

    $targetDir = SongFileUtil::getSongDraftBasePath($cfg, $id, $defaultTargetDir);  

    SongFileUtil::prepareDir($targetDir);

    $now = date("Y-m-d H:i:s", $timestamp);
    $ip = ServerUtil::getRemoteAddress();
    $rawData = $inputPost->getData();
    if(stripos($rawData, ","))
    {
        $rawData = substr($rawData, stripos($rawData, ",")+1);
    } 
    $data = base64_decode($rawData);
    $randomId = $inputPost->getRandomId();

    $songDraft = new SongDraft(null, $database);

    try
    {
        $songDraft->findOneByRandomId($randomId);
    }
    catch(Exception $e)
    {
        $songDraft = new SongDraft(null, $database);
        $path = $targetDir."/".$fileName.".mp3";
        file_put_contents($path, $data);
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

        // get MP3 duration
        $mp3file = new FileMp3($path); 
        $duration = $mp3file->getDuration(); 
        $songDraft->setDuration($duration);

        $songDraft->insert();
    }
}