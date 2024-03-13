<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Constants\HttpHeaderConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Utility\FileUtil;

require_once "inc/auth-with-login-form.php";



$inputGet = new InputGet();
if ($inputGet->getSongId() != null) {
    try {
        $song = new EntitySong(null, $database);
        $song->findOneBySongId($inputGet->getSongId());

        FileUtil::downloadPerSong($inputGet, $song);
    } catch (Exception $e) {
        // do nothing
        echo $e->getMessage();
    }
    exit();
} else if ($inputGet->getAlbumId() != null) {
    $tempDir = FileUtil::prepareTempDir(__DIR__);
    try {
        FileUtil::compressOutput(false);
        $songs = new EntitySong(null, $database);
        $result = $songs->findByAlbumId($inputGet->getAlbumId());
        $album = new Album(null, $database);
        $album->findOneByAlbumId($inputGet->getAlbumId());
        $path = tempnam($tempDir, "tmp_");
        $zip = new ZipArchive();
        foreach ($result->getResult() as $song) {
            FileUtil::downloadSongFiles($zip, $path, $song, true);
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
