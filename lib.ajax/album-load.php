<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;
use MagicObject\Response\Generated\JSONSelectOption;
use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Request\InputGet;
use MagicObject\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new InputGet();
$defautValue = $inputGet->getCurrentValue();
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("album.album_id as id, album.name as value")
    ->from("album")
    ->where("album.active = ? ", true);
$data = new JSONSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($data, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);