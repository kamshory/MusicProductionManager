<?php

namespace Pico\Data\Dto;

use Pico\Data\Entity\Artist;
use Pico\DynamicObject\SetterGetter;

/**
 * Artist DTO
 * @JSON (property-naming-strategy=SNAKE_CASE, prettify=true)
 */
class ArtistDto extends SetterGetter
{
    /**
     * Artist ID
     *
     * @var string
     */
    protected $artistId;

    /**
     * Title
     *
     * @var string
     */
    protected $name;

    /**
     * Gender
     *
     * @var string
     */
    protected $gender;

    /**
     * Birth day
     *
     * @var string
     */
    protected $birthDay;

    /**
     * Active
     *
     * @var bool
     */
    protected $active;

    /**
     * Construct ArtistDto from Artist and not copy other properties
     *
     * @param Artist $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new ArtistDto();
        $output->setArtistId($input->getArtistId());
        $output->setName($input->getName());
        $output->setGender($input->getGender());
        $output->setBirthDay($input->getBirthDay());
        $output->setActive($input->getActive());        
        return $output;
    }
}
