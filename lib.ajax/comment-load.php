<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Request\InputGet;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new InputGet();
$songId = $inputGet->getSongId();
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("song.song_id as song_id, song.title as title, song.comment as comment")
    ->from("song")
    ->where("song.active = ? and song.song_id = ? ", true, $songId);
try
{
    $response = $database->fetch($queryBuilder);
    $restResponse = new PicoResponse();
    $restResponse->sendResponseJSON($response, null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}