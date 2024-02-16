<?php

namespace Pico\Utility;

use Pico\File\FileMp3;

class SongFileUtil
{
    public static function isMp3File($path)
    {
        $mp3file = new FileMp3($path); 
        $duration = $mp3file->getDuration(); 
        return $duration > 0;
    }
    public static function isMidiFile($path)
    {
        $content = self::getContent($path, 100);
        return stripos($content, 'MThd') === 0;
    }
    public static function isXmlMusicFile($path)
    {
        $content = self::getContent($path, 100);
        return stripos($content, '<'.'?'.'xml') === false;
    }
    
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
    
    public static function saveMidiFile($songId, $targetDir, $content)
    {
        $path = $targetDir . "/" . $songId . ".mid";
        file_put_contents($path, $content);
        return $path;
    }
    
    public static function saveXmlMusicFile($songId, $targetDir, $content)
    {
        $path = $targetDir . "/" . $songId . ".xml";
        file_put_contents($path, $content);
        return $path;
    }
}