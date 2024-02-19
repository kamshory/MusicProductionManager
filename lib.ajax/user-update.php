<?php

use Pico\Constants\PicoHttpStatus;
use Pico\Data\Dto\UserDto;
use Pico\Data\Entity\User;
use Pico\Database\PicoPageData;
use Pico\Request\PicoRequest;
use Pico\Response\PicoResponse;

require_once dirname(__DIR__)."/inc/auth.php";
$inputPost = new PicoRequest(INPUT_POST);
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterUsername(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->setActive(true);
$user = new User($inputPost, $database);

/**
 * Check duplicated username
 *
 * @param PicoPageData $duplicated
 * @param string $username
 * @return bool
 */
function checkDuplicated($existing, $username)
{
    if($existing != null)
    {
        $result = $existing->getResult();
        if($result != null && is_array($result))
        {
            foreach($result as $user)
            {
                if($user->getUsername() != $username)
                {
                    return true;
                }
            }
        }
    }
    return false;
}

try
{
    $savedData = new User(null, $database);
    $savedData2 = new User(null, $database);
    $saved = $savedData->findOneUserId($inputPost->getUserId());
    
    // check duplicated username
    $username = $inputPost->getUsername();
    if(!empty($username))
    {
        $existing = $savedData2->findByUsername($username);
        $duplicated = checkDuplicated($existing, $username);
        if(!$duplicated)
        {
            // not duplicated
            $user->setUsername($username);
        }
        $user->setName($inputPost->getName());
        $user->getGender($inputPost->getGender());
    }
    
    
    $user->save();
}
catch(Exception $e)
{
    // do nothing
}
$restResponse = new PicoResponse();
$response = UserDto::valueOf($user);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
