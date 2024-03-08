<?php

namespace MusicProductionManager\Utility;

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
