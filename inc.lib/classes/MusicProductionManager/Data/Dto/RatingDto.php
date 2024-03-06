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
	 * @var string
	 */
	protected $ratingId;

	/**
	 * User ID
	 * 
	 * @var string
	 */
	protected $userId;
	
	/**
	 * User
	 *
	 * @var string
	 */
	protected $user;

	/**
	 * Song ID
	 * 
	 * @var string
	 */
	protected $songId;

	/**
	 * Rating
	 * 
	 * @var double
	 */
	protected $rating;

	/**
	 * Time Create
	 * 
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