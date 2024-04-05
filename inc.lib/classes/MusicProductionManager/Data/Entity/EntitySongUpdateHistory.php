<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_update_history")
 */
class EntitySongUpdateHistory extends MagicObject
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
	 * Song
	 * 
	 * @JoinColumn(name="song_id")
	 * @Label(content="Song")
	 * @var EntitySong
	 */
	protected $song;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

    /**
	 * User
	 * 
	 * @JoinColumn(name="user_id")
	 * @Label(content="User")
	 * @var EntityUser
	 */
	protected $user;

	/**
	 * User Activity ID
	 * 
	 * @Column(name="user_activity_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Activity ID")
	 * @var string
	 */
	protected $userActivityId;

    /**
	 * User Activity
	 * 
	 * @JoinColumn(name="user_activity_id")
	 * @Label(content="User Activity")
	 * @var EntityUserActivity
	 */
	protected $userActivity;

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