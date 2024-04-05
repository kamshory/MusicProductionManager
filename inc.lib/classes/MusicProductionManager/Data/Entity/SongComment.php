<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_comment")
 */
class SongComment extends MagicObject
{
	/**
	 * Song Comment ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_comment_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Song Comment ID")
	 * @var string
	 */
	protected $songCommentId;

	/**
	 * Song ID
	 * 
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Time Start
	 * 
	 * @Column(name="time_start", type="decimal(10,3)", length=103, nullable=true)
	 * @Label(content="Time Start")
	 * @var string
	 */
	protected $timeStart;

	/**
	 * Time End
	 * 
	 * @Column(name="time_end", type="decimal(10,3)", length=103, nullable=true)
	 * @Label(content="Time End")
	 * @var string
	 */
	protected $timeEnd;

	/**
	 * Comment
	 * 
	 * @Column(name="comment", type="longtext", nullable=true)
	 * @Label(content="Comment")
	 * @var string
	 */
	protected $comment;

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