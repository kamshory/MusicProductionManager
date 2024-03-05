<?php

namespace MusicProductionManager\Utility;

use Exception;
use GdImage;
use getID3;
use getid3_writetags;
use MusicProductionManager\Data\Dto\SongFile;
use MusicProductionManager\Exceptions\Mp3FileException;
use MusicProductionManager\File\FileMp3;

class SongFileUtil extends SongUtil
{
    /**
     * Check if file is MP3
     *
     * @param string $path
     * @return boolean
     */
    public static function isMp3File($path)
    {
        $mp3file = new FileMp3($path); 
        $duration = $mp3file->getDuration(); 
        return $duration > 0;
    }

    /**
     * Check if file is MIDI
     *
     * @param string $path
     * @return boolean
     */
    public static function isMidiFile($path)
    {
        $content = self::getContent($path, 100);
        return stripos($content, 'MThd') === 0;
    }

    /**
     * Check if file is XML
     *
     * @param string $path
     * @return boolean
     */
    public static function isXmlMusicFile($path)
    {
        $content = self::getContent($path, 100);
        return stripos($content, '<'.'?'.'xml') !== false;
    }

    /**
     * Check if file is PDF
     *
     * @param string $path
     * @return boolean
     */
    public static function isPdfFile($path)
    {
        $content = self::getContent($path, 100);
        return stripos($content, '%PDF') === 0;
    }

    public static function isImageFile($path)
    {
        try
        {
            $imageSize = getimagesize($path);
            return $imageSize != null && is_array($imageSize);
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
    /**
     * Get file content
     *
     * @param string $path
     * @param integer $max
     * @return string
     */
    public static function getContent($path, $max = 0)
    {
        $fsize = filesize($path);
        if($max > $fsize)
        {
            $max = $fsize;
        }
        $handle = fopen($path, "rb");
        $contents = fread($handle, $max);
        fclose($handle);
        return $contents;
    }
    
    /**
     * Save MIDI file
     *
     * @param string $songId
     * @param string $targetDir
     * @param string $content
     * @return string
     */
    public static function saveMidiFile($songId, $targetDir, $content)
    {
        $path = $targetDir . "/" . $songId . ".mid";
        file_put_contents($path, $content);
        return $path;
    }
    
    /**
     * Save XML file
     *
     * @param string $songId
     * @param string $targetDir
     * @param string $content
     * @return string
     */
    public static function saveXmlMusicFile($songId, $targetDir, $content)
    {
        $path = $targetDir . "/" . $songId . ".xml";
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Save PDF file
     *
     * @param string $songId
     * @param string $targetDir
     * @param string $content
     * @return string
     */
    public static function savePdfFile($songId, $targetDir, $content)
    {
        $path = $targetDir . "/" . $songId . ".pdf";
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Save Image file
     *
     * @param string $songId
     * @param string $targetDir
     * @param GdImage $imageData
     * @return string
     */
    public static function saveImageFile($songId, $targetDir, $imageData)
    {
        $path = $targetDir . "/" . $songId . ".jpg";
        imagejpeg($imageData, $path, 85);
        return $path;
    }
    
    /**
     * Get image content
     *
     * @param string $path
     * @return GdImage|boolean
     */
    public static function getJpegContent($path)
    {
        $imagesize = getimagesize($path);
        if(is_array($imagesize) && count($imagesize) > 2 && $imagesize[0] > 0 && $imagesize[1] > 0)
        {
            
            if($imagesize[2] == IMAGETYPE_JPEG) 
            {
                $imageData = imagecreatefrompng($path);
            }
            else if($imagesize[2] == IMAGETYPE_GIF) 
            {
                $imageData = imagecreatefromgif($path);
            }
            else
            {
                $imageData = imagecreatefromstring(file_get_contents($path));
            }
            return $imageData;
        }
        return false;
    }

    /**
     * Get base diretory of song file
     *
     * @param string $songId
     * @param string $targetDir
     * @return string
     */
    public static function getBaseName($songId, $targetDir)
    {
        return $targetDir."/".$songId;
    }

    public static function prepareDir($targetDir, $permission = 0755)
    {
        return mkdir($targetDir, $permission, true);
    }

    public static function addID3Tag($path, $tagData)
    {
        $getID3 = new getID3;
        assert($getID3 != null, "Can not initialize ID3");

        // Initialize getID3 tag-writing module
        $tagwriter = new getid3_writetags;
        $tagwriter->filename = $path;
        $tagwriter->tagformats = array('id3v2.4');
        $tagwriter->overwrite_tags    = true;
        $tagwriter->remove_other_tags = true;
        $tagwriter->tag_encoding      = 'UTF-8';

        $tagwriter->tag_data = $tagData;

        // write tags
        if ($tagwriter->WriteTags()){
            return true;
        }
        else
        {
            throw new Mp3FileException(implode(' : ', $tagwriter->errors));
        }
    }

    /**
     * Create button
     *
     * @param SongFile $songFile
     * @return string
     */
    public static function createDownloadButton($songFile, $type, $caption, $baseUrl, $target)
    {
        $songId = $songFile->getSongId();
        $exists = false;
        $exists = ($type == 'mp3' && $songFile->getMp3Exists()) || ($type == 'xml' && $songFile->getXmlExists()) || ($type == 'midi' && $songFile->getMidiExists()) || ($type == 'pdf' && $songFile->getPdfExists());       
        if(!$exists)
        {
            $format = '<a href="javascript:;" data-href="%s?type=%s&song_id=%s" class="btn btn-sm btn-tn btn-warning" target="%s"><span class="ti ti-download"></span> %s</a>';
            return sprintf($format, $baseUrl, $type, $songId, $target, $caption);
        }
        else
        {
            $format = '<a href="%s?type=%s&song_id=%s" class="btn btn-sm btn-tn btn-success" target="%s"><span class="ti ti-download"></span> %s</a>';
            return sprintf($format, $baseUrl, $type, $songId, $target, $caption);
        }
    }
}