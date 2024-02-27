<?php

use MagicObject\Request\PicoRequest;
use MusicProductionManager\Constants\HttpHeaderConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\EntitySong;


require_once "inc/auth-with-login-form.php";


/**
 * Download per song
 *
 * @param PicoRequest $inputGet
 * @param EntitySong $song
 * @return void
 */
function downloadPerSong($inputGet, $song)
{
    if ($inputGet->equalsType('mp3') && file_exists($song->getFilePath())) {
        $filename = $song->getName() . ".mp3";
        header(HttpHeaderConstant::CONTENT_TYPE . "audio/mp3");
        header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
        header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePath()));
        readfile($song->getFilePath());
    }
    if ($inputGet->equalsType('midi') && file_exists($song->getFilePathMidi())) {
        $filename = $song->getName() . ".mid";
        header(HttpHeaderConstant::CONTENT_TYPE . "audio/midi");
        header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
        header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePathMidi()));
        readfile($song->getFilePathMidi());
    } else if ($inputGet->equalsType('pdf') && file_exists($song->getFilePathPdf())) {
        $filename = $song->getName() . ".pdf";
        header(HttpHeaderConstant::CONTENT_TYPE . "application/pdf");
        header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
        header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePathPdf()));
        readfile($song->getFilePathPdf());
    } else if ($inputGet->equalsType('xml') && file_exists($song->getFilePathXml())) {
        $filename = $song->getName() . ".xml";
        header(HttpHeaderConstant::CONTENT_TYPE . "application/xml");
        header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
        header(HttpHeaderConstant::CONTENT_LENGTH . filesize($song->getFilePathXml()));
        readfile($song->getFilePathXml());
    } else if ($inputGet->equalsType('all')) {
        $path = tempnam(__DIR__ . "/temp", "tmp_");
        $zip = new ZipArchive();
        downloadSongFiles($zip, $path, $song);
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
function downloadSongFiles($zip, $path, $song, $perAlbum = false)
{
    if ($zip->open($path) === true) {
        if (file_exists($song->getFilePath())) {

            addFileMp3($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathMidi())) {
            addFileMidi($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathPdf())) {
            addFilePdf($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathXml())) {
            addFileXml($zip, $song, $perAlbum);
        }
        if ($perAlbum) {
            $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . ".srt";
        } else {
            $filename = $song->getName() . ".srt";
        }
        $buff = $song->getLyric();
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
function addFileMp3($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . ".mp3";
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
function addFileMidi($zip, $song, $perAlbum)
{
    $buff = file_get_contents($song->getFilePathMidi());
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . ".mid";
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
function addFilePdf($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . ".pdf";
    } else {
        $filename = $song->getName() . ".pdf";
    }
    $buff = file_get_contents($song->getFilePathPdf());
    $zip->addFromString($filename, $buff);
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
function addFileXml($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getName() . ".xml";
    } else {
        $filename = $song->getName() . ".xml";
    }
    $buff = file_get_contents($song->getFilePathXml());
    $zip->addFromString($filename, $buff);
    return $zip;
}

function prepareTempDir()
{
    $dir = __DIR__ . "/temp";
    if (!file_exists($dir)) {
        $created = mkdir($dir, 0755, true);
        if (!$created) {
            echo "Can not create temporary directory";
            exit();
        }
    }
    return $dir;
}

$inputGet = new PicoRequest(INPUT_GET);
if ($inputGet->getSongId() != null) {
    try {
        $song = new EntitySong(null, $database);
        $song->findOneBySongId($inputGet->getSongId());

        downloadPerSong($inputGet, $song);
    } catch (Exception $e) {
        // do nothing
        echo $e->getMessage();
    }
    exit();
} else if ($inputGet->getAlbumId() != null) {
    $tempDir = prepareTempDir();
    try {
        $songs = new EntitySong(null, $database);
        $result = $songs->findByAlbumId($inputGet->getAlbumId());
        $album = new Album(null, $database);
        $album->findOneByAlbumId($inputGet->getAlbumId());
        $path = tempnam($tempDir, "tmp_");
        $zip = new ZipArchive();
        foreach ($result->getResult() as $song) {
            downloadSongFiles($zip, $path, $song, true);
        }
        $zip->close();
        $filename = $album->getName() . ".zip";
        header(HttpHeaderConstant::CONTENT_DISPOSITION . "attachment; filename=\"$filename\"");
        header(HttpHeaderConstant::CONTENT_TYPE . "application/zip");
        header(HttpHeaderConstant::CONTENT_LENGTH . filesize($path));
        readfile($path);
        unlink($path);
    } catch (Exception $e) {
        // do nothing
    }
    exit();
}
