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
     * Add text to existiong PDF file
     * Reference https://www.webniraj.com/2016/09/12/creating-editing-a-pdf-using-php/
     *
     * @param [type] $path
     * @param PicoPdfText[] $textToInsert
     * @return void
     */
    public static function addText($path, $textToInsert)
    {
        $pdf = new Fpdi("l");
        $pdf->setSourceFile($path);
        $tpl = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($tpl);

        if(is_array($textToInsert))
        {
            foreach($textToInsert as $textData)
            {
                $pdf = self::addTextTo($pdf, $textData);
            }
        }
        else
        {
            $pdf = self::addTextTo($pdf, $textToInsert);
        }
    }

    /**
     * Add specified text to PDF object
     *
     * @param Fpdi $pdf
     * @param PicoPdfText $textToInsert
     * @return Fpdi
     */
    private static function addTextTo($pdf, $textToInsert)
    {
        // Set the default font to use
        $pdf->SetFont($textToInsert->fontName);
 
        // set font size
        $pdf->SetFontSize($textToInsert->fontSize); 
        // set position
        $pdf->SetXY($textToInsert->x, $textToInsert->y); 

        // adding a Cell 
        $pdf->Cell( $textToInsert->width, $textToInsert->height, $textToInsert->text, $textToInsert->border, $textToInsert->fill, $textToInsert->align);

        return $pdf;
    }
      
}
