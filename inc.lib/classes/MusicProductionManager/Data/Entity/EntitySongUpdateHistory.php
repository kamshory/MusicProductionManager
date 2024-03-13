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
	 * Song
	 * 
	 * @JoinColumn(name="song_id")
	 * @var EntitySong
	 */
	protected $song;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userId;
    
    /**
	 * User
	 * 
	 * @JoinColumn(name="user_id")
	 * @var EntityUser
	 */
	protected $user;

	/**
	 * User Activity ID
	 * 
	 * @Column(name="user_activity_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userActivityId;
    
    /**
	 * User Activity
	 * 
	 * @JoinColumn(name="user_activity_id")
	 * @var EntityUserActivity
	 */
	protected $userActivity;

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