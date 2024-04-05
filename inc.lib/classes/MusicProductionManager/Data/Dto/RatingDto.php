<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\Rating;

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class RatingDto extends SetterGetter
{
	/**
	 * Rating ID
	 * 
	 * @Label(content="Rating ID")
	 * @var string
	 */
	protected $ratingId;

	/**
	 * User ID
	 * 
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * User
	 * 
	 * @Label(content="User")
	 * @var string
	 */
	protected $user;

	/**
	 * Song ID
	 * 
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songId;

	/**
	 * Rating
	 * 
	 * @Label(content="Rating")
	 * @var double
	 */
	protected $rating;

	/**
	 * Time Create
	 * 
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

    /**
     * Construct RatingDto from Rating and not copy other properties
     * 
     * @param Rating $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new RatingDto();
        $output->setRatingId($input->getRatingId());
        $output->setUserId($input->getUserId());
		$output->setUser($input->hasValueUser() ? $input->getUser()->getName() : null);
        $output->setSongId($input->getSongId());
        $output->setRating($input->getRating());
        $output->setTimeCreate($input->getTimeCreate());
        return $output;
	}
}