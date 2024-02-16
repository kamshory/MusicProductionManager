<?php

namespace Pico\Data\Entity;

use Pico\DynamicObject\DynamicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_attachment")
 */
class SongAttachment extends DynamicObject
{
	/**
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_attachment_id", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $songAttachmentId;

	/**
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $songId;

	/**
	 * @Column(name="name", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * @Column(name="path", type="text", nullable=true)
	 * @var string
	 */
	protected $path;

	/**
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @var integer
	 */
	protected $fileSize;

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

}