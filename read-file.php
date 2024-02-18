<?php

use Pico\Data\Entity\Album;
use Pico\Data\Entity\EntitySong;
use Pico\Request\PicoRequest;

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
        $filename = $song->getTitle() . ".mp3";
        header("Content-type: audio/mp3");
        header("Content-disposition: attachment; filename=\"$filename\"");
        readfile($song->getFilePathMidi());
    }
    if ($inputGet->equalsType('midi') && file_exists($song->getFilePathMidi())) {
        $filename = $song->getTitle() . ".mid";
        header("Content-type: audio/midi");
        header("Content-disposition: attachment; filename=\"$filename\"");
        readfile($song->getFilePathMidi());
    } else if ($inputGet->equalsType('pdf') && file_exists($song->getFilePathPdf())) {
        $filename = $song->getTitle() . ".pdf";
        header("Content-type: application/pdf");
        header("Content-disposition: attachment; filename=\"$filename\"");
        readfile($song->getFilePathMidi());
    } else if ($inputGet->equalsType('xml') && file_exists($song->getFilePathXml())) {
        $filename = $song->getTitle() . ".xml";
        header("Content-type: application/xml");
        header("Content-disposition: attachment; filename=\"$filename\"");
        readfile($song->getFilePathMidi());
    } else if ($inputGet->equalsType('all')) {
        $path = tempnam(__DIR__ . "/temp", "tmp_");
        $zip = new ZipArchive();
        downloadSongFiles($zip, $path, $song);
        $zip->close();
        $filename = $song->getTitle() . ".zip";
        header("Content-disposition: attachment; filename=\"$filename\"");
        header("Content-type: application/zip");
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
 * @param bool $perAlbum
 * @return void
 */
function downloadSongFiles($zip, $path, $song, $perAlbum = false)
{
    if ($zip->open($path) === true) {
        if (file_exists($song->getFilePath())) {

            $zip = addFileMp3($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathMidi())) {
            $zip = addFileMidi($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathPdf())) {
            $zip = addFilePdf($zip, $song, $perAlbum);
        }
        if (file_exists($song->getFilePathXml())) {
            $zip = addFileXml($zip, $song, $perAlbum);
        }
        $filename = $song->getTitle() . ".srt";
        $buff = $song->getLyric();
        $zip->addFromString($filename, $buff);
    }
}

/**
 * Add file mp3
 *
 * @param ZipArchive $zip
 * @param EntitySong $song
 * @param bool $perAlbum
 * @return ZipArchive
 */
function addFileMp3($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getTitle() . ".mp3";
    } else {
        $filename = $song->getTitle() . ".mp3";
    }
    $buff = file_get_contents($song->getFilePath());
    $zip->addFromString($filename, $buff);
    return $zip;
}

/**
 * Add file mid
 *
 * @param ZipArchive $zip
 * @param EntitySong $song
 * @param bool $perAlbum
 * @return ZipArchive
 */
function addFileMidi($zip, $song, $perAlbum)
{
    $buff = file_get_contents($song->getFilePathMidi());
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getTitle() . ".mid";
        $zip->addFromString($filename, $buff);
        $filename = $song->getTitle() . ".mid";
        $zip->addFromString($filename, $buff);
    } else {
        $filename = $song->getTitle() . ".mid";
    }
    $zip->addFromString($filename, $buff);
    return $zip;
}

/**
 * Add file pdf
 *
 * @param ZipArchive $zip
 * @param EntitySong $song
 * @param bool $perAlbum
 * @return ZipArchive
 */
function addFilePdf($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getTitle() . ".pdf";
    } else {
        $filename = $song->getTitle() . ".pdf";
    }
    $buff = file_get_contents($song->getFilePathPdf());
    $zip->addFromString($filename, $buff);
    return $zip;
}

/**
 * Add file xml
 *
 * @param ZipArchive $zip
 * @param EntitySong $song
 * @param bool $perAlbum
 * @return ZipArchive
 */
function addFileXml($zip, $song, $perAlbum)
{
    if ($perAlbum) {
        $filename = sprintf("%02d", $song->getTrackNumber()) . " - " . $song->getTitle() . ".xml";
    } else {
        $filename = $song->getTitle() . ".xml";
    }
    $buff = file_get_contents($song->getFilePathXml());
    $zip->addFromString($filename, $buff);
    return $zip;
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
    try {
        $songs = new EntitySong(null, $database);
        $result = $songs->findByAlbumId($inputGet->getAlbumId());
        $album = new Album(null, $database);
        $album->findOneByAlbumId($inputGet->getAlbumId());
        $path = tempnam(__DIR__ . "/temp", "tmp_");
        $zip = new ZipArchive();
        foreach ($result->getResult() as $song) {
            downloadSongFiles($zip, $path, $song, true);
        }
        $zip->close();
        $filename = $album->getName() . ".zip";
        header("Content-disposition: attachment; filename=\"$filename\"");
        header("Content-type: application/zip");
        readfile($path);
        unlink($path);
    } catch (Exception $e) {
        // do nothing
        echo $e->getMessage();
    }
    exit();
}
