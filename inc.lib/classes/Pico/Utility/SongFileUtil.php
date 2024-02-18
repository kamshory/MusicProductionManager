<?php

namespace Pico\Utility;

use Pico\File\FileMp3;

class SongFileUtil
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
        return stripos($content, '<'.'?'.'xml') === false;
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
}