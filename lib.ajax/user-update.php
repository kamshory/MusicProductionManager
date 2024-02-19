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
$user = new User($inputPost, $database);

/**
 * Check duplicated username
 *
 * @param PicoPageData $duplicated
 * @param string $username
 * @return bool
 */
function checkDuplicatedUsername($existing, $username)
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

/**
 * Check duplicated email
 *
 * @param PicoPageData $duplicated
 * @param string $email
 * @return bool
 */
function checkDuplicatedEmail($existing, $email)
{
    if($existing != null)
    {
        $result = $existing->getResult();
        if($result != null && is_array($result))
        {
            foreach($result as $user)
            {
                if($user->getUsername() != $email)
                {
                    return true;
                }
            }
        }
    }
    return false;
}

/**
 * Validate password
 *
 * @param string $password
 * @return boolean
 */
function isValidPassword($password)
{
    if($password != null & strlen($password) >= 6)
    {
        return true;
    }
    return false;
}

/**
 * Validate date
 *
 * @param string $date
 * @param string $format
 * @return boolean
 */
function isValidDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

try
{
    $savedData = new User(null, $database);
    $savedData2 = new User(null, $database);
    $savedData3 = new User(null, $database);
    $saved = $savedData->findOneUserId($inputPost->getUserId());
    
    // check duplicated username
    $username = $inputPost->getUsername();
    if(!empty($username))
    {
        $existing1 = $savedData2->findByUsername($username);
        
        // set username
        $duplicated = checkDuplicatedUsername($existing1, $username);
        if(!$duplicated)
        {
            // not duplicated
            $user->setUsername($username);
        }
    }
    
    // check duplicated email
    $email = $inputPost->getUsername();
    if(!empty($email))
    {
        $existing2 = $savedData3->findByEmail($email);
        
        // set username
        $duplicated = checkDuplicatedEmail($existing2, $email);
        if(!$duplicated)
        {
            // not duplicated
            $user->setEmail($email);
        }
    }
    
    // set name
    $user->setName($inputPost->getName());
    
    // set gender
    $user->setGender($inputPost->getGender());
    
    // set password
    $password = trim($inputPost->getPassword());
    if(isValidPassword($password))
    {
        $password = hash('sha256', $password);
        $user->setPassword($password);
    }
    
    // set birth_day
    $birthDay = trim($inputPost->getBirthDate());
    if(isValidDate($birthDay))
    {
        $user->setBirthday($birthDay);
    }   
    
    // set active
    $active = $inputPost->getActive();
    if($inputPost->getUserId() == $currentLoggedInUser->getUserId())
    {
        $active = "1";
    }
    $user->setActive($active);
    
    // set blocked
    $blocked = $inputPost->getBlocked();
    if($inputPost->getUserId() == $currentLoggedInUser->getUserId())
    {
        $blocked = "0";
    }
    $user->setBlocked($blocked);
    
    $now = date('Y-m-d H:i:s');
    $user->setTimeCreate($now);
    $user->setTimeEdit($now);
    $user->setIpCreate($_SERVER['REMOTE_ADDR']);
    $user->setIpEdit($_SERVER['REMOTE_ADDR']);
    $user->setAdminCreate(1);
    $user->setAdminEdit(1);
    
    $user->save();
}
catch(Exception $e)
{
    // do nothing
}
$restResponse = new PicoResponse();
$response = UserDto::valueOf($user);
$restResponse->sendResponse($response, 'json', null, PicoHttpStatus::HTTP_OK);
