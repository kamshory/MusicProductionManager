<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="rating")
 */
class Rating extends MagicObject
{
	/**
	 * Rating ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="rating_id", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $ratingId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userId;

	/**
	 * Song ID
	 * 
	 * @Column(name="song_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $songId;

	/**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @var double
	 */
	protected $rating;

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

}