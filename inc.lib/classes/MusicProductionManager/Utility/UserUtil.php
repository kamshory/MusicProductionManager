<?php

namespace MusicProductionManager\Utility;

use Exception;
use MagicObject\Database\PicoDatabase;
use MagicObject\MagicObject;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MusicProductionManager\Constants\UserRole;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Data\Entity\User;
use MusicProductionManager\Data\Entity\UserActivity;
use MusicProductionManager\Data\Entity\UserType;

class UserUtil
{
    /**
     * Get user type
     *
     * @param EntityUser $user
     * @return UserType
     */
    public static function getUserType($user)
    {
        return $user->hasValueUserType() ? $user->getUserType() : new UserType(null);
    }
    
    /**
     * Get artist
     *
     * @param EntityUser $user
     * @return Artist
     */
    public static function getArtist($user)
    {
        return $user->hasValueArtist() ? $user->getArtist() : new Artist(null);
    }
    
    /**
     * Check if user can change producer
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isAllowSelectProducer($user)
    {
        return $user->isAdmin() || self::isUserAsProducer($user);
    }
    
    /**
     * Is user as vocalist
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isUserAsProducer($user)
    {
        return $user->getCurrentRole() == UserRole::PRODUCER && $user->hasValueProducer();
    }
    
    /**
     * Check if user can change vocalist
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isAllowSelectVocalist($user)
    {
        return $user->isAdmin() || self::isUserAsVocalist($user);
    }
    
    /**
     * Is user as vocalist
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isUserAsVocalist($user)
    {
        return $user->getCurrentRole() == UserRole::VOCALIST && $user->hasValueArtist();
    }
    
    /**
     * Check if user can change composer
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isAllowSelectComposer($user)
    {
        return $user->isAdmin() || self::isUserAsComposer($user);
    }
    
    /**
     * Is user as composer
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isUserAsComposer($user)
    {
        return $user->getCurrentRole() == UserRole::COMPOSER && $user->hasValueComposer();
    }
    
    /**
     * Check if user can change arranger
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isAllowSelectArranger($user)
    {
        return $user->isAdmin() || self::isUserAsArranger($user);
    }
    
    /**
     * Is user as arranger
     *
     * @param EntityUser $user
     * @return boolean
     */
    public static function isUserAsArranger($user)
    {
        return $user->getCurrentRole() == UserRole::ARRANGER && $user->hasValueArtist();
    }
    
    /**
     * Get available user role
     *
     * @param EntityUser $user
     * @return string[]
     */
    public static function getAvailableUserRole($user)
    {
        $role = array();
        if($user->isAdmin())
        {
            $role[] = UserRole::ADMIN;
        }
        if($user->hasValueProducer())
        {
            $role[] = UserRole::PRODUCER;
        }
        if($user->hasValueComposer())
        {
            $role[] = UserRole::COMPOSER;
        }
        if($user->hasValueArranger())
        {
            $role[] = UserRole::ARRANGER;
        }
        if($user->hasValueVocalist())
        {
            $role[] = UserRole::VOCALIST;
        }
        return $role;
    }
    
    /**
     * Validate before user update user role
     *
     * @param EntityUser $user
     * @param string $newRole
     * @return boolean
     */
    public static function isValidNewUserRole($user, $newRole)
    {
        $availableRole = self::getAvailableUserRole($user);
        return in_array($newRole, $availableRole);
    }
    
    /**
     * Get current user role
     *
     * @param EntityUser $user
     * @return string
     */
    public static function getCurrentUserRole($user)
    {
        $currentRole = null;
        if($user->isAdmin() && $user->getCurrentRole() == UserRole::ADMIN)
        {
            $currentRole = UserRole::ADMIN;
        }
        else if(self::isUserAsProducer($user))
        {
            $currentRole = UserRole::PRODUCER;
        }
        else if(self::isUserAsComposer($user))
        {
            $currentRole = UserRole::COMPOSER;
        }
        else if(self::isUserAsArranger($user))
        {
            $currentRole = UserRole::ARRANGER;
        }
        else if(self::isUserAsVocalist($user))
        {
            $currentRole = UserRole::VOCALIST;
        }
        return $currentRole;
    }
    
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
     * @param MagicObject $cfg
     * @param PicoDatabase $database
     * @param string $userId
     * @param string $activity
     * @param InputGet|null $inputGet
     * @param InputPost|null $inputPost
     * @param boolean $skipRequestBody
     * @return string
     */
    public static function logUserActivity($cfg, $database, $userId, $activity, $inputGet, $inputPost, $skipRequestBody = false)
    {
        $requestBody = null;
        if(!$skipRequestBody && ($inputPost == null || $inputPost->isEmpty()))
        {
            $requestBody = json_decode(file_get_contents("php://input"));
        }
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];
        $timeCreate = date('Y-m-d H:i:s');
        $ipCreate = ServerUtil::getRemoteAddress($cfg);
        
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
        return $userActivity->getUserActivityId();
    }
}