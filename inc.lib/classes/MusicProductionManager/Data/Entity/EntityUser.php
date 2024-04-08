<?php
namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * User
 * @Entity
 * @Table(name=user)
 */
class EntityUser extends MagicObject
{

	/**
	 * User ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @Column(name="username", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Username")
	 * @Label(content="Username")
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @Column(name="password", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Password")
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @Column(name="admin", type="tinyint(1)", length=1, nullable=true)
	 * @var boolean
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Birth Day")
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email")
	 * @var string
	 */
	protected $email;

	/**
	 * Time Zone
	 * 
	 * @Column(name="time_zone", type="varchar(255)", length=255, nullable=true)
	 * @Label(content="Time Zone")
	 * @var string
	 */
	protected $timeZone;

	/**
	 * User Type ID
	 * 
	 * @Column(name="user_type_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Type ID")
	 * @var string
	 */
	protected $userTypeId;

    /**
	 * User Type ID
	 * 
	 * @JoinColumn(name="user_type_id")
	 * @Label(content="User Type ID")
	 * @var UserType
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @Column(name="associated_artist", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Artist")
	 * @var string
	 */
	protected $associatedArtist;

    /**
	 * Artist
	 * 
	 * @JoinColumn(name="associated_artist")
	 * @Label(content="Artist")
	 * @var Artist
	 */
	protected $artist;

	/**
	 * Associated Producer
	 * 
	 * @Column(name="associated_producer", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Producer")
	 * @var string
	 */
	protected $associatedProducer;

	/**
	 * Associated Producer
	 * 
	 * @JoinColumn(name="associated_producer")
	 * @Label(content="Associated Producer")
	 * @var Producer
	 */
	protected $producer;

	/**
	 * Current Role
	 * 
	 * @Column(name="surrent_role", type="text", nullable=true)
	 * @Label(content="Current Role")
	 * @var string
	 */
	protected $currentRole;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @Label(content="Time Edit")
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @Label(content="Admin Create")
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Admin Edit")
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="IP Create")
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="IP Edit")
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Reset Password Hash
	 * 
	 * @Column(name="reset_password_hash", type="varchar(256)", length=256, nullable=true)
	 * @Label(content="Reset Password Hash")
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @Column(name="last_reset_password", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Reset Password")
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @Column(name="blocked", type="tinyint(1)", length=1, nullable=true)
	 * @var boolean
	 */
	protected $blocked;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $active;

}