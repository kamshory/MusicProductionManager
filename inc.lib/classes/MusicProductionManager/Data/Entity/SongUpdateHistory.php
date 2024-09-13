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
	 * Song Update History ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_update_history_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Song Update History ID")
	 * @var string
	 */
	protected $songUpdateHistoryId;

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
	 * User Activity ID
	 * 
	 * @Column(name="user_activity_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Activity ID")
	 * @var string
	 */
	protected $userActivityId;

	/**
	 * Action
	 * 
	 * @Column(name="action", type="varchar(20)", length=20, nullable=true)
	 * @Label(content="Action")
	 * @var string
	 */
	protected $action;

	/**
	 * Time Update
	 * 
	 * @Column(name="time_update", type="timestamp", length=19, nullable=true)
	 * @Label(content="Time Update")
	 * @var string
	 */
	protected $timeUpdate;

	/**
	 * IP Update
	 * 
	 * @Column(name="ip_update", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="IP Update")
	 * @var string
	 */
	protected $ipUpdate;

}