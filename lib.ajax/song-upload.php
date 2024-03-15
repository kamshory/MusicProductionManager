<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\File\FileMp3;
use MusicProductionManager\File\FileUpload;
use MusicProductionManager\Utility\FileUtilMxl;
use MusicProductionManager\Utility\Id3Tag;
use MusicProductionManager\Utility\ImageUtil;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongFileUtil;
use MusicProductionManager\Utility\SongUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new InputPost();

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
    $defaultTargetDir = dirname(__DIR__)."/files/$id";
    if(stripos($defaultTargetDir, ":") !== false)
    {
        $arr1 = explode(":", $defaultTargetDir, 2);
        $defaultTargetDir = end($arr1);
    }
    
    $tempDir = dirname(__DIR__)."/temp";
    
    
    SongFileUtil::prepareDir($tempDir);
    
    $fileUpload->uploadTemporaryFile($_FILES, 'file', $tempDir, $id, mt_rand(100000, 999999));  

    $path = $fileUpload->getFilePath();
    
    $header = SongFileUtil::getContent($path, 96);
    
    if(SongFileUtil::isMp3File($path))
    {    
        $song->setFileUploadTime($now);
        
        // copy path to mp3Path
        
        $targetDir = SongFileUtil::getSongBasePath($cfg, $defaultTargetDir);
        
        SongFileUtil::prepareDir($targetDir);
        
        $mp3Path = SongFileUtil::getMp3Path($targetDir);
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
        $midiPath = SongFileUtil::getMidiPath($targetDir);
        copy($path, $midiPath);
        $song->setFilePathMidi($midiPath);
        $song->setLastUploadTimeMidi($now);
    }
    else if(SongFileUtil::isXmlMusicFile($path))
    {
        $xmlMusicPath = SongFileUtil::getMusicXmlPath($targetDir);
        copy($path, $midiPath);
        $song->setFilePathXml($xmlMusicPath);
        $song->setLastUploadTimeXml($now);
    }  
    else if(SongFileUtil::isPdfFile($path))
    {
        $pdfPath = SongFileUtil::getScoresPath($targetDir);
        copy($path, $pdfPath);
        $song->setFilePathPdf($pdfPath);
        $song->setLastUploadTimePdf($now);
    }  
    else if(SongFileUtil::isImageFile($path))
    {
        // save image with original dimension
        $jpegPath = SongFileUtil::getScoresPath($targetDir);
        copy($path, $pdfPath);
        ImageUtil::convertToJpeg($jpegPath);

        $song->setFilePathJpeg($jpegPath);
        $song->setLastUploadTimeJpeg($now);

        $mp3Path = $song->getFilePath();
        if($mp3Path != null && !empty($mp3Path) && file_exists($mp3Path))
        {
            $entitySong = new EntitySong(null, $database);
            $entitySong->findOneBySongId($song->getSongId());

            $album = $entitySong->hasValueAlbum() ? $entitySong->getAlbum()->getName() : "";
            $artist = $entitySong->hasValueVocalist() ? $entitySong->getVocalist()->getName() : "";

            $tagData = new Id3Tag;
            $tagData->addAlbum($album);
            $tagData->addArtist($artist);
            $tagData->addComment('Comment');

            $picture = ImageUtil::imageToString(ImageUtil::cropImageCenter(ImageCreateFromJPEG($jpegPath), $cfg->getSongImage()->getWidth(), $cfg->getSongImage()->getHeight()));
            
            $tagData->addPicture($picture, "image/jpeg", $song->getTitle());
            
            SongFileUtil::addID3Tag($mp3Path, $tagData->getTags());
        }
    } 
    else if(SongFileUtil::isZippedFile($path))
    {
        if(FileUtilMxl::isValidMusicXmlFile($path))
        {
            $xmlMusicPath = SongFileUtil::getMusicXmlPath($targetDir);
            
            $list = FileUtilMxl::getFileList($path);
            if(isset($list))
            {
                $val = array_values($list);
                file_put_contents($xmlMusicPath, $val[0]);
                $song->setFilePathXml($xmlMusicPath);
                $song->setLastUploadTimeXml($now);
            }
            
            
        }
    }  
    $songId = $song->getSongId();
    $song->save();


    $song = new EntitySong(null, $database);
    
    $song->findOneBySongId($songId);

    if($song->getFirstUploadTime() == null && $song->getLastUploadTime() != null)
    {
        $song->setFirstUploadTime($song->getLastUploadTime());
        $song->update();
    }
    
    if(file_exists($path))
    {
        unlink($path);
    }

    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $userActivityId = UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Upload song ".$song->getSongId(), $inputGet, $inputPost);
    SongUtil::updateSong($database, $songId, $currentLoggedInUser->getUserId(), "update", $now, ServerUtil::getRemoteAddress($cfg), $userActivityId);

    $restResponse = new PicoResponse();
    $restResponse->sendResponse($song, 'json', null, PicoHttpStatus::HTTP_OK);

}
catch(Exception $e)
{
    // do nothing
    error_log($e->getMessage());
}
