<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\UserTypeDto;
use MusicProductionManager\Data\Entity\UserType;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new InputPost();
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
    $userType->setIpCreate(ServerUtil::getRemoteAddress());
    $userType->setIpEdit(ServerUtil::getRemoteAddress());
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
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Add user type ".$userType->getUserTypeId(), $inputGet, $inputPost);
}
catch(Exception $e)
{
    $userType->insert();
}
$restResponse = new PicoResponse();
$response = UserTypeDto::valueOf($userType);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
