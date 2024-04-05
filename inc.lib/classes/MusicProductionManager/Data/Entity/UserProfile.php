<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="user_profile")
 */
class UserProfile extends MagicObject
{
	/**
	 * User Profile ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="user_profile_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User Profile ID")
	 * @var string
	 */
	protected $userProfileId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Profile Name
	 * 
	 * @Column(name="profile_name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Profile Name")
	 * @var string
	 */
	protected $profileName;

	/**
	 * Profile Value
	 * 
	 * @Column(name="profile_value", type="text", nullable=true)
	 * @Label(content="Profile Value")
	 * @var string
	 */
	protected $profileValue;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @Label(content="Time Edit")
	 * @var string
	 */
	protected $timeEdit;

}