<?php

namespace Pico\Data\Entity;

use Pico\DynamicObject\DynamicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="reference")
 */
class Reference extends DynamicObject
{
	/**
	 * @NotNull
	 * @Column(name="reference_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $referenceId;

	/**
	 * @Column(name="title", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * @Column(name="genre_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $genreId;

	/**
	 * @Column(name="album", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $album;

	/**
	 * @Column(name="artist_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistId;

	/**
	 * @Column(name="year", type="year(4)", length=4, nullable=true)
	 * @var string
	 */
	protected $year;

	/**
	 * @Column(name="url", type="text", nullable=true)
	 * @var string
	 */
	protected $url;

	/**
	 * @Column(name="url_provider", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $urlProvider;

	/**
	 * @Column(name="lyric", type="text", nullable=true)
	 * @var string
	 */
	protected $lyric;

	/**
	 * @Column(name="description", type="longtext", nullable=true)
	 * @var string
	 */
	protected $description;

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
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

}