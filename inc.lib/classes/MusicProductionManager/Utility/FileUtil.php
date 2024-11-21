<?php

namespace MusicProductionManager\Utility;

use MagicObject\Request\PicoRequestBase;
use MusicProductionManager\Constants\HttpHeaderConstant;
use MusicProductionManager\Data\Entity\EntitySong;
use ZipArchive;

class FileUtil
{
    public static function compressOutput($flag = false)
    {
        if($flag)
        {
            ob_start("ob_gzhandler");
        }
    }



    /**
     * Download per song
     *
     * @param PicoRequestBase $inputGet
     * @param EntitySong $song
     * @return void
     */
    public static function downloadPerSong($inputGet, $song)
    {
        if ($inputGet->equalsType('mp3') && file_exists($song->getFilePath())) {
            self::compressOutput(false);
            $filename = $song->getName() . ".mp3";
            header(HttpHeaderConstant::CONTENT_TYPE . "audio/mp3");
            header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
            header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePath()));
            readfile($song->getFilePath());
        }
        if ($inputGet->equalsType('midi') && file_exists($song->getFilePathMidi())) {
            self::compressOutput(true);
            $filename = $song->getName() . ".mid";
            header(HttpHeaderConstant::CONTENT_TYPE . "audio/midi");
            header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
            header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePathMidi()));
            readfile($song->getFilePathMidi());
        } else if ($inputGet->equalsType('pdf') && file_exists($song->getFilePathPdf())) {

            $name = $song->getName();
            $title = $song->issetTitle() ? ("(".$song->getTitle().")") : $song->getName();
            $composer = $song->issetComposer() ? $song->getComposer()->getName() : "NN";
            
            $songTitle = SongUtil::getPdfTitle($song);

            $fontName = "Times";

            $textToInsert = array(
                (new PicoPdfText())
                    ->setPosition(105, 12)
                    ->setDimension(100, 8)
                    ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
                    ->setFontName($fontName)
                    ->setFontSize(18)
                    ->setText($name)
                    ->setFillColor(new PicoColor(255, 255, 255))
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignCenter(),
                (new PicoPdfText())
                    ->setPosition(105, 19.5)
                    ->setDimension(100, 6)
                    ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
                    ->setFontName($fontName)
                    ->setFontSize(13)
                    ->setText($title)
                    ->setFillColor(new PicoColor(255, 255, 255))
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignCenter(),
                (new PicoPdfText())
                    ->setPosition(197, 28)
                    ->setDimension(40, 6)
                    ->setFontName($fontName)
                    ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_RIGHT)
                    ->setFontSize(11)
                    ->setText($composer)
                    ->setFillColor(new PicoColor(255, 255, 255))
                    ->setTextColor(new PicoColor(0, 0, 0))
                    ->alignRight()
                
            );
            $footerFormat = str_replace(array("%s", "%d"), "", $song->getName()) . " (%d of %d)"; 
        
            $textNextPage = (new PicoPdfText())
                ->setPosition(105, 12)
                ->setDimension(40, 8)
                ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
                ->setFontName($fontName)
                ->setFontSize(11)
                ->setText($footerFormat)
                ->setFillColor(new PicoColor(255, 255, 255))
                ->setTextColor(new PicoColor(0, 0, 0))
                ->alignCenter();
                
            $pdf = FileUtilPdf::addText($song->getFilePathPdf(), $textToInsert, $textNextPage);
            $pdf = FileUtilPdf::addLyric($pdf, $name, $title, $composer, $song->getLyricMidi());
            
            $pdf->SetTitle($songTitle);
            $content = FileUtilPdf::pdfToString($pdf);        
            $content = FileUtilPdf::replacePdfTitle($content, $song);
            
            self::compressOutput(true);
            $filename = $song->getName() . ".pdf";
            header(HttpHeaderConstant::CONTENT_TYPE . "application/pdf");
            header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
            header(HttpHeaderConstant::CONTENT_LENGTH . strlen($content));

            echo $content;
            

        } else if ($inputGet->equalsType('xml') && file_exists($song->getFilePathXml())) {
            self::compressOutput(true);
            $filename = $song->getName() . ".xml";
            header(HttpHeaderConstant::CONTENT_TYPE . "application/xml");
            header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
            header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePathXml()));
            readfile($song->getFilePathXml());
        } else if ($inputGet->equalsType('all')) {
            self::compressOutput(false);
            $path = tempnam(__DIR__ . "/temp", "tmp_");
            $zip = new ZipArchive();
            self::downloadSongFiles($zip, $path, $song);
            $zip->close();
            $filename = $song->getName() . ".zip";
            header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
            header(HttpHeaderConstant::CONTENT_TYPE . "application/zip");
            header(HttpHeaderConstant::CONTENT_LENGTH . filesize($path));
            readfile($path);
            unlink($path);
        }
    }

    /**
     * Download song files
     *
     * @param ZipArchive $zip
     * @param string $path
     * @param EntitySong $song
     * @param boolean $perAlbum
     * @return void
     */
    public static function downloadSongFiles($zip, $path, $song, $perAlbum = false)
    {
        if ($zip->open($path) === true) {
            if (file_exists($song->getFilePath())) {
                self::addFileMp3($zip, $song, $perAlbum);
            }
            if (file_exists($song->getFilePathMidi())) {
                self::addFileMidi($zip, $song, $perAlbum);
            }
            if (file_exists($song->getFilePathPdf())) {
                self::addFilePdf($zip, $song, $perAlbum);
            }
            if (file_exists($song->getFilePathXml())) {
                self::addFileXml($zip, $song, $perAlbum);
            }
            if ($perAlbum) {
                $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . " - " . $song->getTitle() . ".srt";
            } else {
                $filename = $song->getName() . ".srt";
            }
            $buff = $song->getSubtitle();
            $zip->addFromString($filename, $buff);
        }
    }

    /**
     * Add file mp3 to ZIP archive
     *
     * @param ZipArchive $zip
     * @param EntitySong $song
     * @param boolean $perAlbum
     * @return ZipArchive
     */
    public static function addFileMp3($zip, $song, $perAlbum)
    {
        if ($perAlbum) {
            $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . " - " . $song->getTitle() . ".mp3";
        } else {
            $filename = $song->getName() . ".mp3";
        }
        $buff = file_get_contents($song->getFilePath());
        $zip->addFromString($filename, $buff);
        return $zip;
    }

    /**
     * Add file mid to ZIP archive
     *
     * @param ZipArchive $zip
     * @param EntitySong $song
     * @param boolean $perAlbum
     * @return ZipArchive
     */
    public static function addFileMidi($zip, $song, $perAlbum)
    {
        $buff = file_get_contents($song->getFilePathMidi());
        if ($perAlbum) {
            $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . " - " . $song->getTitle() . ".mid";
            $zip->addFromString($filename, $buff);
            $filename = str_replace(" ", "", $song->getName()) . ".mid";
            $zip->addFromString($filename, $buff);
        } else {
            $filename = $song->getName() . ".mid";
        }
        $zip->addFromString($filename, $buff);
        return $zip;
    }

    /**
     * Add file pdf to ZIP archive
     *
     * @param ZipArchive $zip
     * @param EntitySong $song
     * @param boolean $perAlbum
     * @return ZipArchive
     */
    public static function addFilePdf($zip, $song, $perAlbum)
    {
        if ($perAlbum) {
            $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . " - " . $song->getTitle() . ".pdf";
        } else {
            $filename = $song->getName() . ".pdf";
        }

        $name = $song->getName();
        $title = $song->issetTitle() ? ("(".$song->getTitle().")") : $song->getName();
        $composer = $song->issetComposer() ? $song->getComposer()->getName() : "NN";

        if($song->issetTitle())
        {
            $songTitle = $song->getTitle();
        }
        else
        {
            $songTitle = $song->getName();
        }
        if($song->issetComposer())
        {
            $songTitle .= ' by '.$song->getComposer()->getName();
        }

        $fontName = "Times";

        $textToInsert = array(
            (new PicoPdfText())
                ->setPosition(105, 12)
                ->setDimension(100, 8)
                ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
                ->setFontName($fontName)
                ->setFontSize(18)
                ->setText($name)
                ->setFillColor(new PicoColor(255, 255, 255))
                ->setTextColor(new PicoColor(0, 0, 0))
                ->alignCenter(),
            (new PicoPdfText())
                ->setPosition(105, 19.5)
                ->setDimension(100, 6)
                ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
                ->setFontName($fontName)
                ->setFontSize(13)
                ->setText($title)
                ->setFillColor(new PicoColor(255, 255, 255))
                ->setTextColor(new PicoColor(0, 0, 0))
                ->alignCenter(),
            (new PicoPdfText())
                ->setPosition(197, 28)
                ->setDimension(40, 6)
                ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_RIGHT)
                ->setFontName($fontName)
                ->setFontSize(11)
                ->setText($composer)
                ->setFillColor(new PicoColor(255, 255, 255))
                ->setTextColor(new PicoColor(0, 0, 0))
                ->alignRight()
            
        );
        
        $footerFormat = str_replace(array("%s", "%d"), "", $song->getName()) . " (%d of %d)"; 
        
        $textNextPage = (new PicoPdfText())
            ->setPosition(105, 12)
            ->setDimension(40, 8)
            ->setStyle(0, 0, FileUtilPdf::PDF_ALIGN_CENTER)
            ->setFontName($fontName)
            ->setFontSize(11)
            ->setText($footerFormat)
            ->setFillColor(new PicoColor(255, 255, 255))
            ->setTextColor(new PicoColor(0, 0, 0))
            ->alignCenter();

        $pdf = FileUtilPdf::addText($song->getFilePathPdf(), $textToInsert, $textNextPage);
        $pdf = FileUtilPdf::addLyric($pdf, $name, $title, $composer, $song->getLyricMidi());
        
        $pdf->SetTitle($songTitle);
        $content = FileUtilPdf::pdfToString($pdf);        
        $content = FileUtilPdf::replacePdfTitle($content, $song);
        $zip->addFromString($filename, $content);
        return $zip;
    }

    /**
     * Add file xml to ZIP archive
     *
     * @param ZipArchive $zip
     * @param EntitySong $song
     * @param boolean $perAlbum
     * @return ZipArchive
     */
    public static function addFileXml($zip, $song, $perAlbum)
    {
        if ($perAlbum) {
            $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . " - " . $song->getTitle() . ".xml";
        } else {
            $filename = $song->getName() . ".xml";
        }
        $buff = file_get_contents($song->getFilePathXml());
        $zip->addFromString($filename, $buff);
        return $zip;
    }

    /**
     * Prepare temporary directory
     *
     * @param string $baseDir
     * @return string
     */
    public static function prepareTempDir($baseDir)
    {
        $dir = $baseDir . "/temp";
        if (!file_exists($dir)) {
            $created = mkdir($dir, 0755, true);
            if (!$created) {
                echo "Can not create temporary directory";
                exit();
            }
        }
        return $dir;
    }
}