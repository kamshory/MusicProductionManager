<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\UserDto;
use Pico\Data\Entity\User;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter name
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->setActive(true);
$user = new User($inputPost, $database);

try
{
    $savedData = new User(null, $database);
    $saved = $savedData->findOneByName($inputPost->getName());
    if($saved->getUserId() != "")
    {
        $user->setUserId($saved->getUserId());
    }
    else
    {
        $user->save();
    }  
}
catch(Exception $e)
{
    $user->insert();
}
$restResponse = new PicoResponse();
$response = UserDto::valueOf($user);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
