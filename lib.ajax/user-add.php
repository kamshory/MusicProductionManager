<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\UserDto;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";
$inputPost = new InputPost();
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
$inputPost->checkboxAdmin(false);
$inputPost->checkboxBlocked(false);
// filter name
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
// filter stage name
$inputPost->setActive(true);


/**
 * Validate password
 *
 * @param string $password
 * @return boolean
 */
function isValidPassword($password)
{
    if ($password != null & strlen($password) >= 6) {
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
function isValidDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

$user = new EntityUser(null, $database);


try {
    $name = $inputPost->getName();
    $username = $inputPost->getUsername();
    $password = $inputPost->getPassword();
    if (!empty($name) && !empty($username) && isValidPassword($password)) {
        $email = $inputPost->getEmail();
        if (!empty($email)) {
            $user->setEmail($email);
        }
        $user->setUsername($username);


        // set name
        $user->setName($inputPost->getName());

        // set gender
        $user->setGender($inputPost->getGender());

        // set password
        $password = trim($inputPost->getPassword());
        if (isValidPassword($password)) {
            $password = hash('sha256', $password);
            $user->setPassword($password);
        }

        // set birth_day
        $birthDay = trim($inputPost->getBirthDay());
        if (isValidDate($birthDay)) {
            $user->setBirthDay($birthDay);
        }

        // set admin
        $admin = $inputPost->getAdmin();
        $user->setAdmin($admin);

        // set active
        $active = $inputPost->getActive();
        $user->setActive($active);

        // set blocked
        $blocked = $inputPost->getBlocked();
        $user->setBlocked($blocked);

        $now = date('Y-m-d H:i:s');
        $user->setTimeCreate($now);
        $user->setTimeEdit($now);
        $user->setIpCreate(ServerUtil::getRemoteAddress($cfg));
        $user->setIpEdit(ServerUtil::getRemoteAddress($cfg));
        $user->setAdminCreate($currentLoggedInUser->getUserId());
        $user->setAdminEdit($currentLoggedInUser->getUserId());

        $user->insert();

        if(!isset($inputGet))
        {
            $inputGet = new InputGet();
        }
        $inputPost->unsetPassword();
        UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Add user ".$user->getUserId(), $inputGet, $inputPost);
    } else {
        // do nothing
    }

    $restResponse = new PicoResponse();
    $response = UserDto::valueOf($user);
    $restResponse->sendResponse($response, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
} catch (Exception $e) {
    // do nothing
}
