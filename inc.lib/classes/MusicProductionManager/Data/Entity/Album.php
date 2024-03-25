<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="album")
 */
class Album extends MagicObject
{
	/**
	 * Album ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $albumId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $name;
	
	/**
	 * Title
	 * 
	 * @Column(name="title", type="text", nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * Description
	 * 
	 * @Column(name="description", type="longtext", nullable=true)
	 * @var string
	 */
	protected $description;
	
	/**
	 * Producer ID
	 * 
	 * @Column(name="producer_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $producerId;

	/**
	 * Release Date
	 * 
	 * @Column(name="release_date", type="date", nullable=true)
	 * @var string
	 */
	protected $releaseDate;

	/**
	 * Number Of Song
	 * 
	 * @Column(name="number_of_song", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $numberOfSong;

	/**
	 * Duration
	 * 
	 * @Column(name="duration", type="float", nullable=true)
	 * @var double
	 */
	protected $duration;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Sort Order
	 * 
	 * @Column(name="sort_order", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $sortOrder;

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
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

	/**
	 * As Draft
	 * 
	 * @Column(name="as_draft", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $asDraft;

}