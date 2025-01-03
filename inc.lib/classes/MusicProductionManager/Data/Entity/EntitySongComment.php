<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_comment")
 */
class EntitySongComment extends MagicObject
{
	/**
	 * Song ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_comment_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songCommentId;

	/**
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="@Column(name="song_id", type="varchar(40)", length=40, nullable=true)")
	 * @var string
	 */
	protected $songId;

	/**
	 * @JoinColumn(name="song_id", referenceColumName="song_id")
	 * @Label(content="@JoinColumn(name="song_id", referenceColumName="song_id")")
	 * @var Song
	 */
	protected $song;

	/**
	 * @Column(name="comment", type="text", nullable=true)
	 * @Label(content="@Column(name="comment", type="text", nullable=true)")
	 * @var string
	 */
	protected $comment;

	/**
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="@Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @Label(content="@Column(name="time_edit", type="timestamp", length=19, nullable=true)")
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @Label(content="@Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)")
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * @JoinColumn(name="admin_create")
	 * @Label(content="@JoinColumn(name="admin_create")")
	 * @var User
	 */
	protected $creator;

	/**
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="@Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)")
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * @JoinColumn(name="admin_edit")
	 * @Label(content="@JoinColumn(name="admin_edit")")
	 * @var User
	 */
	protected $editor;


	/**
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="@Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)")
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="@Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)")
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $active;

}