<?php
namespace Pico\Data\Entity;

use Pico\DynamicObject\DynamicObject;

/**
 * User
 * @Entity
 * @Table(name=user)
 */
class User extends DynamicObject
{    

    /**
     * User ID
     *
     * @var string
     * @Column(name=user_id)
     * @Id
     */
    protected $userId;

    /**
     * Username
     *
     * @var string
     * @Column(name=username)
     */
    protected $username;

    /**
     * Email
     *
     * @var string
     * @Column(name=email)
     */
    protected $email;

    /**
     * Password
     *
     * @var string
     * @Column(name=password)
     */
    protected $password;

    /**
     * Birth day
     *
     * @var string
     * @Column(name=birth_day)
     */
    protected $birthDay;

    /**
     * Gender
     *
     * @var string
     * @Column(name=gender)
     */
    protected $gender;

    /**
     * Name
     *
     * @var string
     * @Column(name=name)
     */
    protected $name;

    /**
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * @Column(name="admin_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * @Column(name="admin_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

    /**
     * Blocked
     *
     * @var bool
     * @Column(name=blocked)
     */
    protected $blocked;

    /**
     * Active
     *
     * @var bool
     * @Column(name=active)
     */
    protected $active;

}