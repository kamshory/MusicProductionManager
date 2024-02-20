<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Response\Generated\PicoSelectOption;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$defautValue = trim(@$_GET['current_value']);
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("genre.genre_id as id, genre.name as value")
    ->from("genre")
    ->where("genre.active = ? ", true)
    ->orderBy("genre.sort_order asc");
$response = new PicoSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);