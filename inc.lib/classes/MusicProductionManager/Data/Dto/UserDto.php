<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\EntityUser;

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class UserDto extends SetterGetter
{
	/**
	 * User ID
	 * 
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @var bool
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @var string
	 */
	protected $email;

	/**
	 * User Type ID
	 * 
	 * @var string
	 */
	protected $userTypeId;
    
    /**
	 * User Type
	 * 
	 * @var string
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @var string
	 */
	protected $associatedArtist;
    
    /**
	 * Artist
	 * 
	 * @var string
	 */
	protected $artist;

	/**
	 * Image Path
	 * 
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Time Create
	 * 
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * IP Create
	 * 
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Reset Password Hash
	 * 
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @var bool
	 */
	protected $blocked;

	/**
	 * Active
	 * 
	 * @var bool
	 */
	protected $active;

    /**
     * Construct UserDto from User and not copy other properties
     * 
     * @param EntityUser $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new UserDto();
        $output->setUserId($input->getUserId);
        $output->setUsername($input->getUsername);
        $output->setPassword($input->getPassword);
        $output->setAdmin($input->getAdmin);
        $output->setName($input->getName);
        $output->setBirthDay($input->getBirthDay);
        $output->setGender($input->getGender);
        $output->setEmail($input->getEmail);
        $output->setUserTypeId($input->getUserTypeId);
        $output->setUserType($input->hasValueUserType() ?$input->getUserType()->getName() : null);
        $output->setAssociatedArtist($input->getAssociatedArtist);
        $output->setArtist($input->hasValueArtist() ?$input->getArtist()->getName() : null);
        $output->setImagePath($input->getImagePath);
        $output->setTimeCreate($input->getTimeCreate);
        $output->setTimeEdit($input->getTimeEdit);
        $output->setAdminCreate($input->getAdminCreate);
        $output->setAdminEdit($input->getAdminEdit);
        $output->setIpCreate($input->getIpCreate);
        $output->setIpEdit($input->getIpEdit);
        $output->setResetPasswordHash($input->getResetPasswordHash);
        $output->setLastResetPassword($input->getLastResetPassword);
        $output->setBlocked($input->getBlocked);
        $output->setActive($input->getActive);
        return $output;
    }


}