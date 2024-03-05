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
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @Column(name="username", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @Column(name="password", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @Column(name="admin", type="tinyint(1)", length=1, nullable=true)
	 * @var bool
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $email;

	/**
	 * User Type ID
	 * 
	 * @Column(name="user_type_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userTypeId;
    
    /**
	 * User Type ID
	 * 
	 * @JoinColumn(name="user_type_id")
	 * @var UserType
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @Column(name="associated_artist", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $associatedArtist;
    
    /**
	 * Artist
	 * 
	 * @JoinColumn(name="associated_artist")
	 * @var Artist
	 */
	protected $artist;
	
	/**
	 * Associated Producer
	 * 
	 * @Column(name="associated_producer", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $associatedProducer;
	
	/**
	 * Associated Producer
	 * 
	 * @JoinColumn(name="associated_producer")
	 * @var Producer
	 */
	protected $producer;
	
	/**
	 * Current Role
	 * 
	 * @Column(name="surrent_role", type="text", nullable=true)
	 * @var string
	 */
	protected $currentRole;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Reset Password Hash
	 * 
	 * @Column(name="reset_password_hash", type="varchar(256)", length=256, nullable=true)
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @Column(name="last_reset_password", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @Column(name="blocked", type="tinyint(1)", length=1, nullable=true)
	 * @var bool
	 */
	protected $blocked;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

}