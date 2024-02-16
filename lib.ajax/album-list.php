<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Database\PicoDatabaseQueryBuilder;
use Pico\Response\Generated\PicoSelectOption;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$defautValue = trim(@$_GET['current_value']);
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("album.album_id as id, album.name as value")
    ->from("album")
    ->where("album.active = ? ", true)
    ->orderBy("album.sort_order desc");
$response = new PicoSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);