<?php

namespace MusicProductionManager\Utility;

use Exception;
use MusicProductionManager\Data\Entity\EntitySong;
use setasign\Fpdi\Fpdi;

class FileUtilPdf
{
    /**
    * Download per song
    *
    * @param string $content
    * @param EntitySong $song
    * @return string
    */
    public static function replacePdfTitle($content, $song)
    {
        if($song->hasValueTitle())
        {
            $title = $song->getTitle();
        }
        else
        {
            $title = $song->getName();
        }
        if($song->hasValueComposer())
        {
            $title .= ' by '.$song->getComposer()->getName();
        }
        $title = str_replace(['(', ')'], '', $title);
        return preg_replace('/\/Title \(.*\)/', '/Title (' . $title . ')', $content);
    }

    /**
     * Add text to first page existiong PDF file
     * Reference https://www.webniraj.com/2016/09/12/creating-editing-a-pdf-using-php/
     *
     * @param [type] $path
     * @param PicoPdfText|PicoPdfText[] $textFirstPage
     * @param PicoPdfText|PicoPdfText[] $textNextPage
     * @return Fpdi
     */
    public static function addText($path, $textFirstPage, $textNextPage)
    {
        $pdf = new Fpdi();
        $numberOfPage = $pdf->setSourceFile($path);

        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tpl);
        
        if(is_array($textFirstPage))
        {
            foreach($textFirstPage as $textData)
            {
                $pdf = self::addTextTo($pdf, $textData);
            }
        }
        else
        {
            $pdf = self::addTextTo($pdf, $textFirstPage);
        }

        $fmt = self::getFisrt($textNextPage);

        for($i = 2; $i <= $numberOfPage; $i++)
        {
            $tpl = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tpl);   
            
            
            if(is_array($textNextPage))
            {
                foreach($textNextPage as $textData)
                {
                    $textData->setText(sprintf($fmt, $i, $numberOfPage));
                    $pdf = self::addTextTo($pdf, $textData);
                }
            }
            else
            {
                $textNextPage->setText(sprintf($fmt, $i, $numberOfPage));
                $pdf = self::addTextTo($pdf, $textNextPage);
            }
        }
        return $pdf;
    }
    
    /**
     * Add lyric
     *
     * @param Fpdi $pdf
     * @param string $name
     * @param string $title
     * @param string $composer
     * @param string $lyricMidi
     * @return Fpdi
     */
    public static function addLyric($pdf, $name, $title, $composer, $lyricMidi)
    {
        try
        { 
            $jsonObj = json_decode($lyricMidi);
            if ($jsonObj === null && json_last_error() !== JSON_ERROR_NONE) {
                return $pdf;
            }
            $lyric = "";
            foreach($jsonObj as $value)
            {
                $lyric .= $value->text;
            }
            
            $lyric = StringUtil::fixingCariageReturn($lyric);
            $lyric = trim($lyric, "\r\n");
            
            
            
            $fontName = "Times";
            
            $nameObj = 
                (new PicoPdfText())
                    ->setPosition(105, 8)
                    ->setDimension(100, 8)
                    ->setStyle(0, 0, "C")
                    ->setFontName($fontName)
                    ->setFontSize(18)
                    ->setText($name)
                    ->setFillColor(new PicoColor(255, 255, 255))
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignCenter();
            $itleObj =
                (new PicoPdfText())
                    ->setPosition(105, 15.5)
                    ->setDimension(100, 6)
                    ->setStyle(0, 0, "C")
                    ->setFontName($fontName)
                    ->setFontSize(13)
                    ->setText($title)
                    ->setFillColor(new PicoColor(255, 255, 255))
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignCenter();
            $composerObj =
                (new PicoPdfText())
                    ->setPosition(197, 22)
                    ->setDimension(40, 6)
                    ->setStyle(0, 0, "R")
                    ->setFontName($fontName)
                    ->setFontSize(11)
                    ->setText($composer)
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignRight();

            
            
            $lyrics = explode("\r\n", $lyric);

            $groupLyrics = self::splitLyric($lyrics);
            
            foreach($groupLyrics as $idx=>$lyrics)
            {
                if($idx == 0)
                {
                    $pdf->AddPage();
                    $pdf = self::addTextTo($pdf, $nameObj);
                    $pdf = self::addTextTo($pdf, $itleObj);
                    $pdf = self::addTextTo($pdf, $composerObj);
                }
                if($idx % 2 != 0)
                {
                    $offsetX = 109;
                }
                else
                {
                    $offsetX = 14;
                }
                $offsetY = 30;
                foreach($lyrics as $index=>$lyricText)
                {
                    $top = ($index * 4) + $offsetY;
                    $lyricObj =
                    (new PicoPdfText())
                        ->setPosition($offsetX, $top)
                        ->setDimension(170, 5)
                        ->setStyle(0, 0, "L")
                        ->setFontName($fontName)
                        ->setFontSize(10)
                        ->setText($lyricText)
                        ->setTextColor(new PicoColor(0, 0, 0))
                        ->alignLeft();
                    $pdf = self::addTextTo($pdf, $lyricObj);
                }
            }
        }
        catch(Exception $e)
        {
            // do nothing
        }
        return $pdf;
    }
    
    /**
     * Split lyric
     *
     * @param array $lyrics
     * @return array
     */
    public static function splitLyric($lyrics)
    {
        $maxLen = self::getMaxLength($lyrics);
        
        $split = false;
        $threshold = 50;
        $maxLine = 50;
        $groupLyrics = array();
        if(self::mustSplit($split, $maxLen, $maxLine, $threshold, $lyrics))
        {
            // split into groups
            // maximum
            $idx = 0;
            if(count($lyrics) < 100)
            {
                // search \r\n after line 40
                foreach($lyrics as $ln=>$text)
                {
                    if($ln > 40 && $text == "")
                    {
                        $idx++;
                    }
                    if(!isset($groupLyrics[$idx]))
                    {
                        $groupLyrics[$idx] = array();
                    }
                    $groupLyrics[$idx][] = $text;
                }
            }
        }
        else
        {
            $groupLyrics = array($lyrics);
        }
        return $groupLyrics;
    }
    
    /**
     * Get maximum length of lyric
     *
     * @param array $lyrics
     * @return void
     */
    public static function getMaxLength($lyrics)
    {
        $maxLen = 0;
        foreach($lyrics as $line)
        {
            $len = strlen($line);
            if($len > $maxLen)
            {
                $maxLen = $len;
            }
        }
        return $maxLen;
    }
    
    /**
     * Check if lyric must be split or note
     *
     * @param bool $split
     * @param integer $maxLen
     * @param integer $maxLine
     * @param integer $threshold
     * @param array $lyrics
     * @return bool
     */
    public static function mustSplit($split, $maxLen, $maxLine, $threshold, $lyrics)
    {
        return $split && $maxLen < $threshold && count($lyrics) > $maxLine;
    }
    
    /**
     * Get fisrt text
     *
     * @param PicoPdfText|PicoPdfText[] $textNextPage
     * @return string
     */
    public static function getFisrt($textNextPage)
    {
        $fmt = null;
        if(is_array($textNextPage))
        {
            foreach($textNextPage as $textData)
            {
                if($fmt == null)
                {
                    $fmt = $textData->getText();
                }
            }
        }
        else
        {
            if($fmt == null)
            {
                $fmt = $textNextPage->getText();
            }
        }
        return $fmt;
    }

    /**
     * Add specified text to PDF object
     *
     * @param Fpdi $pdf
     * @param PicoPdfText $textFirstPage
     * @return Fpdi
     */
    private static function addTextTo($pdf, $textFirstPage)
    {
        // Set the default font to use
        $pdf->SetFont($textFirstPage->fontName);

        // set font size
        $pdf->SetFontSize($textFirstPage->fontSize); 
        // set position
        $pdf->SetXY($textFirstPage->x, $textFirstPage->y); 

        // adding a Cell 
        if($textFirstPage->getFillColor() != null)
        {
            $pdf->SetFillColor($textFirstPage->fillColor->red, $textFirstPage->fillColor->green, $textFirstPage->fillColor->blue);
            $pdf->Rect($textFirstPage->x, $textFirstPage->y, $textFirstPage->width, $textFirstPage->height, "F");
        }
        $pdf->Cell( $textFirstPage->width, $textFirstPage->height, $textFirstPage->text, $textFirstPage->border, $textFirstPage->fill, $textFirstPage->align);
        return $pdf;
    }

    /**
     * Get binary data
     *
     * @param Fpdi $pfd
     * @return string
     */
    public static function pdfToString($pdf)
    {
        ob_start();
        $pdf->Output();
        $stringdata = ob_get_contents(); // read from buffer
        ob_end_clean(); // delete buffer
        return $stringdata;
    }
}
