<?php

namespace Pico\Data\Dto;

use Pico\Data\Entity\User;
use Pico\DynamicObject\SetterGetter;

/**
 * User Type DTO
 * @JSON (property-naming-strategy=SNAKE_CASE)
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
     * Email
     *
     * @var string
     */
    protected $email;

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
     * Birth day
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
     * Name
     *
     * @var string
     */
    protected $name;

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
     * Construct UserDto from UserType and not copy other properties
     *
     * @param User $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new UserDto();
        $output->setUserId($input->getUserId());
        $output->setUsername($input->getUsername());
        $output->setEmail($input->getEmail());
        $output->setName($input->getName());
        $output->setBirthDay($input->getBirthDay());
        $output->setGender($input->setGender());
        $output->setBlocked($input->getBlocked());
        $output->setAdmin($input->getAdmin());        
        $output->setActive($input->getActive());        
        return $output;
    } 
}
