<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="reference")
 */
class Reference extends MagicObject
{
	/**
	 * Reference ID
	 * 
	 * @NotNull
	 * @Column(name="reference_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $referenceId;

	/**
	 * Title
	 * 
	 * @Column(name="title", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * Genre ID
	 * 
	 * @Column(name="genre_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $genreId;

	/**
	 * Album
	 * 
	 * @Column(name="album", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $album;

	/**
	 * Artist ID
	 * 
	 * @Column(name="artist_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistId;

	/**
	 * Year
	 * 
	 * @Column(name="year", type="year(4)", length=4, nullable=true)
	 * @var string
	 */
	protected $year;

	/**
	 * Url
	 * 
	 * @Column(name="url", type="text", nullable=true)
	 * @var string
	 */
	protected $url;

	/**
	 * Url Provider
	 * 
	 * @Column(name="url_provider", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $urlProvider;

	/**
	 * Subtitle
	 * 
	 * @Column(name="subtitle", type="text", nullable=true)
	 * @var string
	 */
	protected $subtitle;

	/**
	 * Description
	 * 
	 * @Column(name="description", type="longtext", nullable=true)
	 * @var string
	 */
	protected $description;

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
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $active;

}