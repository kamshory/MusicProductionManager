<?php

namespace MusicProductionManager\Utility;

use Exception;
use MusicProductionManager\Data\Entity\EntitySong;
use setasign\Fpdi\Fpdi;

class FileUtilPdf
{
    const LYRIC_FONT_NAME = "Times";
    const LYRIC_FONT_SIZE = 12;
    const LYRIC_LINE_HEIGHT = 7;
    const LYRIC_THRESHOLD = 45;
    const LYRIC_MAX_LINE = 35;
    const LYRIC_START_FIND_SPACE = 25;
    const LYRIC_OFFSET_X_1 = 14;
    const LYRIC_OFFSET_X_2 = 109;
    const LYRIC_OFFSET_Y = 38;

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
     * Check if error JSON occured
     *
     * @param array $jsonObj
     * @return boolean
     */
    private static function isErrorJson($jsonObj)
    {
        return $jsonObj === null && json_last_error() !== JSON_ERROR_NONE;
    }

    /**
     * Join text lyric from JSON
     *
     * @param array $jsonObj
     * @return string
     */
    private static function joinLyric($jsonObj)
    {
        $lyric = "";
        foreach($jsonObj as $value)
        {
            $lyric .= $value->text;
        }
        return $lyric;
    }

    /**
     * Check if multiple group
     *
     * @return boolean
     */
    private static function isMultipleGroup($groupLyrics)
    {
        return count($groupLyrics) > 1;
    }

    /**
     * Get total page
     *
     * @param array $groupLyrics
     * @param boolean $split
     * @return integer
     */
    public static function getTotalPage($groupLyrics, $split = false)
    {
        if($split)
        {
            return ceil(count($groupLyrics)/2);
        }
        else
        {
            return count($groupLyrics);
        }
    }

    /**
     * Get current page
     *
     * @param integer $idx
     * @param boolean $split
     * @return integer
     */
    public static function getCurrentPage($idx, $split = false)
    {
        if($split)
        {
            return ceil($idx / 2) + 1;
        }
        else
        {
            return $idx + 1;
        }
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
            if (self::isErrorJson($jsonObj)) {
                return $pdf;
            }
            
            $lyric = self::joinLyric($jsonObj);           
            $lyric = StringUtil::fixingCariageReturn($lyric);
            $lyric = trim($lyric, "\r\n");
            
            $fontName = self::LYRIC_FONT_NAME;
            
            $nameObj = 
                (new PicoPdfText())
                    ->setPosition(105, 12)
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
                    ->setPosition(105, 19.5)
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
                    ->setPosition(197, 28)
                    ->setDimension(40, 6)
                    ->setStyle(0, 0, "R")
                    ->setFontName($fontName)
                    ->setFontSize(11)
                    ->setText($composer)
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignRight();         
            
            $lyrics = explode("\r\n", $lyric);

            $groupLyrics = self::splitLyric($lyrics);
            $groupLyrics = self::trimLyricGroup($groupLyrics);
            $lineHeight = self::LYRIC_LINE_HEIGHT;
            $split = self::isMultipleGroup($groupLyrics);

            $numPage = self::getTotalPage($groupLyrics, $split);
            
            foreach($groupLyrics as $idx=>$lyrics)
            {
                $pageNumber = self::getCurrentPage($idx, $split);
                if($idx % 2 != 0)
                {
                    $offsetX = self::LYRIC_OFFSET_X_2;
                }
                else
                {
                    $offsetX = self::LYRIC_OFFSET_X_1;

                    $pdf->AddPage();
                    $pdf = self::splitPage($pdf, $split);
                    $pdf = self::addTextTo($pdf, $nameObj);
                    $pdf = self::addTextTo($pdf, $itleObj);
                    $pdf = self::addTextTo($pdf, $composerObj);
                    
                    
                    $headerObj =
                    (new PicoPdfText())
                        ->setPosition(14, 10)
                        ->setDimension(55, 6)
                        ->setStyle(0, 0, "L")
                        ->setFontName($fontName)
                        ->setFontSize(10)
                        ->setText("Page $pageNumber of $numPage")
                        ->setTextColor(new PicoColor(0, 0, 0))
                        ->alignLeft();
                    $pdf = self::addTextTo($pdf, $headerObj);
                }
                foreach($lyrics as $index=>$lyricText)
                {
                    
                    $top = ($index * $lineHeight) + self::LYRIC_OFFSET_Y;
                    $lyricObj =
                        (new PicoPdfText())
                            ->setPosition($offsetX, $top)
                            ->setDimension(170, $lineHeight)
                            ->setStyle(0, 0, "L")
                            ->setFontName($fontName)
                            ->setFontSize(self::LYRIC_FONT_SIZE)
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
     * Split page with vertical line
     *
     * @param Fpdi $pdf
     * @param boolean $split
     * @return Fpdi
     */
    public static function splitPage($pdf, $split)
    {
        if($split)
        {
            $pdf->SetDrawColor(188,188,188);
            $pdf->Line(105, self::LYRIC_OFFSET_Y, 105, 285);
        }
        return $pdf;
    }
    
    /**
     * Trim group lyric
     *
     * @param array $groupLyrics
     * @return array
     */
    public static function trimLyricGroup($groupLyrics)
    {
        foreach($groupLyrics as $key=>$value)
        {
            $groupLyrics[$key] = explode("\r\n", trim(implode("\r\n", $value), "\r\n"));
        }
        return $groupLyrics;
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
        
        $split = true;
        $groupLyrics = array();
        if(self::mustSplit($split, $maxLen, self::LYRIC_MAX_LINE, self::LYRIC_THRESHOLD, $lyrics))
        {
            // split into groups
            // maximum
            $cnt = 0;
            $idx = 0;
            foreach($lyrics as $text)
            {
                if($cnt > self::LYRIC_START_FIND_SPACE && $text == "")
                {
                    $idx++;
                    $cnt = 0;
                }
                if(!isset($groupLyrics[$idx]))
                {
                    $groupLyrics[$idx] = array();
                }
                $groupLyrics[$idx][] = $text;
                $cnt++;
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
     * @return integer
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
     * @param boolean $split
     * @param integer $maxLen
     * @param integer $maxLine
     * @param integer $threshold
     * @param array $lyrics
     * @return boolean
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
