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
     * @param bool $skipPostData
     * @param bool $skipRequestBody
     * @return void
     */
    public static function logUserActivity($database, $userId, $activity, $skipPostData = false, $skipRequestBody = false)
    {
        $inputGet = new PicoRequest(INPUT_GET);
        if($skipPostData)
        {
            $inputPost = null;
        }
        else
        {
            $inputPost = new PicoRequest(INPUT_POST);
        }
        $requestBody = null;
        if(!$skipRequestBody && $inputPost->isEmpty())
        {
            $requestBody = json_decode(file_get_contents("php://input"));
        }
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];
        
        $data = array(
            'name' => $activity,
            'path' => $path,
            'user_id' => $userId,
            'method' => $method,
            'get_data' => $inputGet,
            'post_data' => $inputPost,
            'request_body' => $requestBody
        );
        $userActivity = new UserActivity($data, $database);
        $userActivity->insert();

    }
}