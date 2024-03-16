<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\SongDraft;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SongFileUtil;

require_once dirname(__DIR__)."/inc/auth.php";

$baseDir = dirname(__DIR__) . "/song-draft";
SongFileUtil::prepareDir($baseDir);

$now = date("Y-m-d H:i:s");
$ip = ServerUtil::getRemoteAddress();
$inputPost = new InputPost();
$data = base64_decode($inputPost->getData());
$randomId = $inputPost->getRandomId();

$songDraft = new SongDraft(null, $database);

try
{
    $songDraft->findOneByRandomId($randomId);
}
catch(Exception $e)
{
    $songDraft = new SongDraft(null, $database);
    $fileName = $database->generateNewId();
    $path = $baseDir."/".$fileName.".mp3";
    file_put_contents($path, $data);
    $songDraft->setSha1File(sha1_file($path));
    $songDraft->setRandomId($randomId);
    $songDraft->setFilePath($path);
    $songDraft->setAdminCreate($currentLoggedInUser->getUserId());
    $songDraft->setAdminEdit($currentLoggedInUser->getUserId());
    $songDraft->setTimeCreate($now);
    $songDraft->setTimeEdit($now);
    $songDraft->setIpCreate($ip);
    $songDraft->setIpEdit($ip);
    $songDraft->insert();
}

