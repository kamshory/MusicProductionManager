<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\Album;


/**
 * Album DTO
 * @JSON (property-naming-strategy=SNAKE_CASE)
 */
class AlbumDto extends SetterGetter
{
    /**
     * Album ID
     *
     * @var string
     */
    protected $albumId;

    /**
     * Title
     *
     * @var string
     */
    protected $name;

    /**
     * Release date
     *
     * @var string
     */
    protected $releaseDate;

    /**
     * Number of song
     *
     * @var integer
     */
    protected $numberOfSong;

    /**
     * Total duration
     *
     * @var float
     */
    protected $duration;

    /**
     * Active
     *
     * @var boolean
     */
    protected $active;

    /**
     * Construct AlbumDto from Album and not copy other properties
     *
     * @param Album $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new AlbumDto();
        $output->setAlbumId($input->getAlbumId());
        $output->setName($input->getName());
        $output->setReleaseDate($input->getReleaseDate());
        $output->setNumberOfSong($input->getNumberOfSong());
        $output->setDuration(round($input->getDuration(), 3));
        $output->setActive($input->getActive());        
        return $output;
    }
}
