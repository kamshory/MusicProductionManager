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
    ->select("producer.producer_id as id, producer.name as value")
    ->from("producer")
    ->where("producer.active = ? ", true)
    ->orderBy("producer.name asc");
$response = new JSONSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);