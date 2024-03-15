<?php

namespace MusicProductionManager\Utility;

use DOMDocument;
use Exception;
use MusicProductionManager\Data\Entity\EntitySong;
use setasign\Fpdi\Fpdi;
use ZipArchive;

class FileUtilMxl
{
    /**
     * Check if file is valid MusicXML file
     *
     * @param string $path Zip file path
     * @return bool
     */
    public static function isValidMusicXmlFile($path)
    {
        $list = array();
        try {
            $zip = new ZipArchive;
            if ($zip->open($path) === true) {
                
                $xmlContainer = $zip->getFromName("META-INF/container.xml");
                if($xmlContainer !== false)
                {
                    $files = self::getRootFiles($xmlContainer);
                    if($files != null && is_array($files))
                    {
                        foreach($files as $file)
                        {
                            $list[] = $file;
                        }
                    }
                }
                $zip->close();
                return count($list) > 0;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * Get file list
     *
     * @param string $path Zip file path
     * @return array|bool Associated array with key is file name and value is content
     */
    public static function getFileList($path)
    {
        $list = array();
        try {
            $zip = new ZipArchive;
            if ($zip->open($path) === true) {
                
                $xmlContainer = $zip->getFromName("META-INF/container.xml");
                if($xmlContainer !== false)
                {
                    $files = self::getRootFiles($xmlContainer);
                    if($files != null && is_array($files))
                    {
                        foreach($files as $file)
                        {
                            $content = $zip->getFromName($file);
                            if($content !== false)
                            {
                                $list[$files] = $content;
                            }
                        }
                    }
                }
                $zip->close();
                return $list;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get file list
     *
     * @param string $xmlContainer
     * @return string[]
     */
    public static function getRootFiles($xmlContainer)
    {
        $domdoc = new DOMDocument();
        $domdoc->loadXML($xmlContainer);
        $nodes = $domdoc->getElementsByTagName("container");
        $list = array();
        foreach($nodes as $node)
        {       
            $response = $node->getElementsByTagName("rootfiles");
            foreach($response as $info)
            {
                $test = $info->getElementsByTagName("rootfile");
                $fullPath = $test->item(0)->getAttribute('full-path');
                
                // expected application/vnd.recordare.musicxml+xml
                $mimeType = $test->item(0)->getAttribute('media-type');
                if(self::isMusicXml($fullPath, $mimeType))
                {
                    $list[] = $fullPath;
                }
            }
        }
        return $list;
    }
    
    /**
     * Get file extension
     *
     * @param string $path
     * @return string
     */
    public static function getFileExtension($path)
    {
        if(stripos($path, ".") !== false)
        {
            $arr = explode(".", $path);
            return end($arr);
        }
        return $path;
    }
    
    /**
     * Check file extension and mime
     *
     * @param string $fullPath
     * @param string $mimeType
     * @return bool
     */
    public static function isMusicXml($fullPath, $mimeType)
    {
        $extension = strtolower(self::getFileExtension($fullPath));
        $segments = preg_split("/\+|\//", strtolower($mimeType));
        return
            ($extension == "xml" || $extension == "musicxml") 
            && 
            (in_array("application", $segments) && (in_array("vnd.recordare.musicxml", $segments) || in_array("xml", $segments)))
        ;
    }
}
