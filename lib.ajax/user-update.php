<?php

use MagicObject\Constants\PicoHttpStatus;
use MagicObject\Constants\PicoMime;
use MagicObject\Database\PicoPageData;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Response\PicoResponse;
use MusicProductionManager\Data\Dto\UserDto;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Data\Entity\User;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__) . "/inc/auth.php";
$inputPost = new InputPost();
// check box only sent if it checeked
// if active is null, then set to false
$inputPost->checkboxActive(false);
// filter
$inputPost->filterName(FILTER_SANITIZE_SPECIAL_CHARS);
$inputPost->filterUsername(FILTER_SANITIZE_SPECIAL_CHARS);

$user = new EntityUser(null, $database);

/**
 * Check duplicated username
 *
 * @param PicoPageData $duplicated
 * @param string $username
 * @return bool
 */
function checkDuplicatedUsername($existing, $username)
{
    if ($existing != null) {
        $result = $existing->getResult();
        if ($result != null && is_array($result)) {
            foreach ($result as $user) {
                if ($user->getUsername() != $username) {
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
    if ($existing != null) {
        $result = $existing->getResult();
        if ($result != null && is_array($result)) {
            foreach ($result as $user) {
                if ($user->getUsername() != $email) {
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
 * @return bool
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
 * @return bool
 */
function isValidDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

try {
    $user->setUserId($inputPost->getUserId());

    $savedData1 = new User(null, $database);
    $savedData2 = new User(null, $database);
    $savedData3 = new User(null, $database);
    $saved = $savedData1->findOneUserId($inputPost->getUserId());

    // check duplicated username
    $username = $inputPost->getUsername();
    if (!empty($username)) {
        $existing1 = $savedData2->findByUsername($username);

        // set username
        $duplicated = checkDuplicatedUsername($existing1, $username);
        if (!$duplicated) {
            // not duplicated
            $user->setUsername($username);
        }
    }

    // check duplicated email
    $email = $inputPost->getEmail();
    if (!empty($email)) {
        $existing2 = $savedData3->findByEmail($email);

        // set username
        $duplicated = checkDuplicatedEmail($existing2, $email);
        if (!$duplicated) {
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
    if (isValidPassword($password)) {
        $password = hash('sha256', $password);
        $user->setPassword($password);
    }

    // set birth_day
    $birthDay = trim($inputPost->getBirthDay());
    if (isValidDate($birthDay)) {
        $user->setBirthDay($birthDay);
    }

    // set active
    $active = $inputPost->getActive();
    if ($inputPost->getUserId() == $currentLoggedInUser->getUserId()) {
        $active = "1";
    }
    $user->setActive($active);

    // set admin
    $admin = $inputPost->getAdmin();
    if ($inputPost->getUserId() != $currentLoggedInUser->getUserId()) {
        $user->setAdmin($admin);
    }

    $user->setAssociatedArtist($inputPost->getAssociatedArtist());

    // set blocked
    $blocked = $inputPost->getBlocked();
    if ($inputPost->getUserId() == $currentLoggedInUser->getUserId()) {
        $blocked = "0";
    }
    $user->setBlocked($blocked);

    $now = date('Y-m-d H:i:s');
    $user->setTimeCreate($now);
    $user->setTimeEdit($now);
    $user->setIpCreate(ServerUtil::getRemoteAddress($cfg));
    $user->setIpEdit(ServerUtil::getRemoteAddress($cfg));
    $user->setAdminCreate($currentLoggedInUser->getUserId());
    $user->setAdminEdit($currentLoggedInUser->getUserId());

    $user->save();
    
    if(!isset($inputGet))
    {
        $inputGet = new InputGet();
    }
    $inputPost->unsetPassword();
    UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Update user ".$inputPost->getUserId(), $inputGet, $inputPost);

} catch (Exception $e) {
    // do nothing
}
$restResponse = new PicoResponse();
$response = UserDto::valueOf($user);
$restResponse->sendResponse($response, PicoMime::APPLICATION_JSON, null, PicoHttpStatus::HTTP_OK);
