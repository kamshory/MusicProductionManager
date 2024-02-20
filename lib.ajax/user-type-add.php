<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\UserTypeDto;
use MusicProductionManager\Data\Entity\UserType;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new PicoRequest(INPUT_POST);
$inputPost->checkboxAdmin(false);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->setActive(true);
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
    
    $savedData = new UserType(null, $database);
    $saved = $savedData->findOneByName($inputPost->getName());
    if($saved->getUserTypeId() != "")
    {
        $userType->setUserTypeId($saved->getUserTypeId());
    }
    else
    {
        $userType->save();
    }  
}
catch(Exception $e)
{
    $userType->insert();
}
$restResponse = new PicoResponse();
$response = UserTypeDto::valueOf($userType);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
