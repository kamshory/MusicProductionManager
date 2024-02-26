<?php

namespace MusicProductionManager\Utility;

use Exception;
use MagicObject\Database\PicoDatabase;
use MagicObject\Request\PicoRequest;
use MusicProductionManager\Data\Entity\User;
use MusicProductionManager\Data\Entity\UserActivity;

class UserUtil
{
    /**
     * Check if username is duplicated or not
     *
     * @param PicoDatabase $database
     * @param string $userId
     * @param string $username
     * @return boolean
     */
    public static function isDuplicatedUsername($database, $userId, $username)
    {
        try
        {
            $user = new User(null, $database);
            $result = $user->findByUsername($username);
            $rows = $result->getResult();
            foreach($rows as $row)
            {
                if($row->getUserId() != $userId)
                {
                    return true;
                }
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * Check if email is duplicated or not
     *
     * @param PicoDatabase $database
     * @param string $userId
     * @param string $email
     * @return boolean
     */
    public static function isDuplicatedEmail($database, $userId, $email)
    {
        try
        {
            $user = new User(null, $database);
            $result = $user->findByEmail($email);
            $rows = $result->getResult();
            foreach($rows as $row)
            {
                if($row->getUserId() != $userId)
                {
                    return true;
                }
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * Log user activity
     *
     * @param PicoDatabase $database
     * @param string $userId
     * @param string $activity
     * @param PicoRequest $inputGet
     * @param PicoRequest $inputPost
     * @param boolean $skipRequestBody
     * @return void
     */
    public static function logUserActivity($database, $userId, $activity, $inputGet, $inputPost, $skipRequestBody = false)
    {
        $requestBody = null;
        if(!$skipRequestBody && ($inputPost == null || $inputPost->isEmpty()))
        {
            $requestBody = json_decode(file_get_contents("php://input"));
        }
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];
        $timeCreate = date('Y-m-d H:i:s');
        $ipCreate = $_SERVER['REMOTE_ADDR'];
        
        $data = array(
            'name' => $activity,
            'path' => $path,
            'userId' => $userId,
            'method' => $method,
            'getData' => $inputGet == null || $inputGet->isEmpty() ? null : $inputGet,
            'postData' => $inputPost,
            'requestBody' => $requestBody,
            'timeCreate' => $timeCreate,
            'ipCreate' => $ipCreate
        );
        $userActivity = new UserActivity($data, $database);
        $userActivity->insert();

    }
}