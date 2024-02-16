<?php

namespace Pico\Data\Entity;

use Pico\DynamicObject\DynamicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="album")
 */
class Album extends DynamicObject
{
	/**
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $albumId;

	/**
	 * @Column(name="name", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $name;
	
	/**
	 * @Column(name="description", type="longtext", nullable=true)
	 * @var string
	 */
	protected $description;

	/**
	 * @Column(name="release_date", type="date", nullable=true)
	 * @var string
	 */
	protected $releaseDate;

	/**
	 * @Column(name="number_of_song", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $numberOfSong;

	/**
	 * @Column(name="duration", type="float", nullable=true)
	 * @var double
	 */
	protected $duration;

	/**
	 * @Column(name="sort_order", type="int", nullable=true)
	 * @var integer
	 */
	protected $sortOrder;

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
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

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
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

	/**
	 * @Column(name="as_draft", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $asDraft;

}