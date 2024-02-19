<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\UserTypeDto;
use Pico\Data\Entity\UserType;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";

$inputPost = new PicoRequest(INPUT_POST);
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->checkboxAdmin(false);
$inputPost->checkboxActive(false);

$userTypeId = $inputPost->getUserTypeId();
$name = $inputPost->getName();
if(empty($userTypeId) || empty($name))
{
    exit();
}

$userType = new UserType($inputPost, $database);

try
{
    $now = date('Y-m-d H:i:s');
    $userType->setTimeCreate($now);
    $userType->setTimeEdit($now);
    $userType->setIpCreate($_SERVER['REMOTE_ADDR']);
    $userType->setIpEdit($_SERVER['REMOTE_ADDR']);
    $userType->setAdminCreate($currentLoggedInUser->getUserId());
    $userType->setAdminEdit($currentLoggedInUser->getUserId());
    
    $userType->update();
    $restResponse = new PicoResponse();
    $response = UserTypeDto::valueOf($userType);
    $restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
}
catch(Exception $e)
{
    // do nothing
}
