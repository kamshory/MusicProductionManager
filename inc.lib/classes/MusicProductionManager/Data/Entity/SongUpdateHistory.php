<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_update_history")
 */
class SongUpdateHistory extends MagicObject
{
	/**
	 * Song Update History
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_update_history", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $songUpdateHistory;

	/**
	 * Song ID
	 * 
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $songId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userId;

	/**
	 * Action
	 * 
	 * @Column(name="action", type="varchar(20)", length=20, nullable=true)
	 * @var string
	 */
	protected $action;

	/**
	 * Time Update
	 * 
	 * @Column(name="time_update", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeUpdate;

	/**
	 * IP Update
	 * 
	 * @Column(name="ip_update", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipUpdate;

}