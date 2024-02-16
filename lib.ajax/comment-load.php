<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Database\PicoDatabaseQueryBuilder;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new PicoRequest(INPUT_GET);
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