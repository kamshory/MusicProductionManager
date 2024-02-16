<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\ArtistDto;
use Pico\Data\Entity\Artist;
use Pico\Request\PicoFilterConstant;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->filterStageName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$artist = new Artist($inputPost, $database);

try
{
    $artist->save();
    $restResponse = new PicoResponse();
    $response = ArtistDto::valueOf($artist);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
