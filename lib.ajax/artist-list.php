<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Database\PicoDatabaseQueryBuilder;
use Pico\Request\PicoRequest;
use Pico\Response\Generated\PicoSelectOption;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputGet = new PicoRequest(INPUT_GET);

$defautValue = $inputGet->getCurrentValue();
$queryBuilder = new PicoDatabaseQueryBuilder($database);
$queryBuilder
    ->newQuery()
    ->select("artist.artist_id as id, artist.name as value")
    ->from("artist")
    ->where("artist.active = ? ", true);
$response = new PicoSelectOption($database, $queryBuilder, $defautValue);
$restResponse = new PicoResponse();
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);