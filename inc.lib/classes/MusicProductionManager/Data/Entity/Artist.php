<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="artist")
 */
class Artist extends MagicObject
{
	/**
	 * Artist ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="artist_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Artist ID")
	 * @var string
	 */
	protected $artistId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Stage Name
	 * 
	 * @Column(name="stage_name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Stage Name")
	 * @var string
	 */
	protected $stageName;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="date", nullable=true)
	 * @Label(content="Birth Day")
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Phone
	 * 
	 * @Column(name="phone", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Phone")
	 * @var string
	 */
	protected $phone;

	/**
	 * Phone2
	 * 
	 * @Column(name="phone2", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Phone2")
	 * @var string
	 */
	protected $phone2;

	/**
	 * Phone3
	 * 
	 * @Column(name="phone3", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Phone3")
	 * @var string
	 */
	protected $phone3;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email")
	 * @var string
	 */
	protected $email;

	/**
	 * Email2
	 * 
	 * @Column(name="email2", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email2")
	 * @var string
	 */
	protected $email2;

	/**
	 * Email3
	 * 
	 * @Column(name="email3", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email3")
	 * @var string
	 */
	protected $email3;

	/**
	 * Website
	 * 
	 * @Column(name="website", type="text", nullable=true)
	 * @Label(content="Website")
	 * @var string
	 */
	protected $website;

	/**
	 * Address
	 * 
	 * @Column(name="address", type="text", nullable=true)
	 * @Label(content="Address")
	 * @var string
	 */
	protected $address;

	/**
	 * Picture
	 * 
	 * @Column(name="picture", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Picture")
	 * @var bool
	 */
	protected $picture;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Image Update
	 * 
	 * @Column(name="image_update", type="timestamp", length=19, nullable=true)
	 * @Label(content="Image Update")
	 * @var string
	 */
	protected $imageUpdate;

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
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @Label(content="Active")
	 * @var bool
	 */
	protected $active;

}