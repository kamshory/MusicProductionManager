<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="draft_rating")
 */
class DraftRating extends MagicObject
{
	/**
	 * Draft Rating ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="draft_rating_id", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $draftRatingId;

	/**
	 * User ID
	 * 
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userId;

	/**
	 * Song Draft ID
	 * 
	 * @Column(name="song_draft_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $songDraftId;

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