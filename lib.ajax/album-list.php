<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;

use MagicObject\Response\PicoResponse;
use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\JSONSelectOption;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new InputGet();
$defautValue = $inputGet->getCurrentValue();
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("album.album_id as id, album.name as value")
    ->from("album")
    ->where("album.active = ? ", true)
    ->orderBy("album.sort_order desc");
$response = new JSONSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);