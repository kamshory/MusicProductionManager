<?php
namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * Rating
 * @Entity
 * @Table (name=rating)
 */
class Rating extends MagicObject
{
    /**
     * Rating ID
     *
     * @var string
     * @Column(name=rating_id)
     * @Id
     */
    protected $ratingId;

    /**
     * Song ID
     *
     * @var string
     * @Column(name=song_id)
     */
    protected $songId;

        /**
     * User ID
     *
     * @var string
     * @Column(name=user_id)
     */
    protected $userId;

    /**
     * Rate
     * @var float
     * @Column(name=rate)
     */
    protected $rate;

    /**
     * Active
     *
     * @var bool
     * @Column(name=active)
     */
    protected $active;
}