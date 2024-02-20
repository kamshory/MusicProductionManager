<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Response\Generated\PicoSelectOption;
use MagicObject\Response\PicoResponse;
use MagicObject\Constants\PicoHttpStatus;

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