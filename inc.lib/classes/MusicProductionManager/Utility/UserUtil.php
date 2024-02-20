<?php

namespace MusicProductionManager\Utility;

use Exception;
use MagicObject\Database\PicoDatabase;
use MusicProductionManager\Data\Entity\User;


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
}