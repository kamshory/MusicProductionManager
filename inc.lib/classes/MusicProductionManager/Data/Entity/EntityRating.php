<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="rating")
 */
class EntityRating extends MagicObject
{
	/**
	 * Rating ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="rating_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Rating ID")
	 * @var string
	 */
	protected $ratingId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

    /**
	 * @JoinColumn(name="user_id", referenceColumName="user_id")
	 * @Label(content="@JoinColumn(name="user_id", referenceColumName="user_id")")
	 * @var User
	 */
	protected $user;


	/**
	 * Song ID
	 * 
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songId;

	/**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @Label(content="Rating")
	 * @var double
	 */
	protected $rating;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

}