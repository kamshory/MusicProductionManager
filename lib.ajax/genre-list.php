<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\JSONSelectOption;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new InputGet();
$defautValue = $inputGet->getCurrentValue();
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("genre.genre_id as id, genre.name as value")
    ->from("genre")
    ->where("genre.active = ? ", true)
    ->orderBy("genre.sort_order asc");
$response = new JSONSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);