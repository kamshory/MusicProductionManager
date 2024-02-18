<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Entity\Song;
use Pico\File\FileMp3;
use Pico\File\FileUpload;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;
use Pico\Utility\SongFileUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);

$id = $inputPost->getSongId();
if(empty($id))
{
    $id = $database->generateNewId();
}
$randomSongId = $inputPost->getRandomSongId();

try
{
    $song = new Song(null, $database);
    $song->setActive(true);
    $song->setSongId($id);

    $now = date('Y-m-d H:i:s');


    $song->setRandomSongId($randomSongId);
    $song->setTimeCreate($now);
    
    

    // get uploaded file properties
    $fileUpload = new FileUpload();
    $targetDir = dirname(__DIR__)."/files";
    
    $tempDir = dirname(__DIR__)."/temp";
    
    if(!file_exists($targetDir))
    {
        mkdir($targetDir, 0755, true);
    }
    if(!file_exists($tempDir))
    {
        mkdir($tempDir, 0755, true);
    }
    
    $fileUpload->uploadTemporaryFile($_FILES, 'file', $tempDir, $id, mt_rand(100000, 999999));    
    $path = $fileUpload->getFilePath();
    
    $header = SongFileUtil::getContent($path, 96);
    
    if(SongFileUtil::isMp3File($path))
    {    
        $song->setFileUploadTime($now);
        
        // copy path to mp3Path
        $mp3Path = $targetDir . "/" . $id . ".mp3";
        copy($path, $mp3Path);
        $song->setFilePath($mp3Path);
        
        $song->setFileName(basename($mp3Path));
        $song->setFileSize($fileUpload->getFileSize());
        $song->setFileType($fileUpload->getFileType());
        $song->setFileExtension($fileUpload->getFileExtension());
        $song->setFileMd5(md5_file($mp3Path));
        $song->setLastUploadTime($now);
        
        // get MP3 duration
        $mp3file = new FileMp3($mp3Path); 
        $duration = $mp3file->getDuration(); 
        $song->setDuration($duration);
        
    }
    else if(SongFileUtil::isMidiFile($path))
    {
        $midiPath = SongFileUtil::saveMidiFile($id, $targetDir, file_get_contents($path));
        $song->setFilePathMidi($midiPath);
        $song->setLastUploadTimeMidi($now);
    }
    else if(SongFileUtil::isXmlMusicFile($path))
    {
        $xmlMusicPath = SongFileUtil::saveXmlMusicFile($id, $targetDir, file_get_contents($path));
        $song->setFilePathXml($xmlMusicPath);
        $song->setLastUploadTimeXml($now);
    }  
    else if(SongFileUtil::isPdfFile($path))
    {
        $xmlMusicPath = SongFileUtil::savePdfFile($id, $targetDir, file_get_contents($path));
        $song->setFilePathPdf($xmlMusicPath);
        $song->setLastUploadTimePdf($now);
    }  
    
    $song->save();
    $song->select();
    if($song->getFirstUploadTime() == null && $song->getLastUploadTime() != null)
    {
        $song->setFirstUploadTime($song->getLastUploadTime());
        $song->save();
    }
    
    if(file_exists($path))
    {
        unlink($path);
    }

    $restResponse = new PicoResponse();
    $restResponse->sendResponse($song, 'json', null, PicoHttpStatus::HTTP_OK);

}
catch(Exception $e)
{
    // do nothing
}
