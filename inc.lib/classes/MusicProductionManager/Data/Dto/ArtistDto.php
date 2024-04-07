<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\Artist;


/**
 * Artist DTO
 * @JSON (property-naming-strategy=SNAKE_CASE, prettify=true)
 */
class ArtistDto extends SetterGetter
{
    /**
     * Artist ID
     * 
     * @Label(content="Artist ID")
     * @var string
     */
    protected $artistId;

    /**
     * Title
     * 
     * @Label(content="Title")
     * @var string
     */
    protected $name;

    /**
     * Gender
     * 
     * @Label(content="Gender")
     * @var string
     */
    protected $gender;

    /**
     * Birth day
     * 
     * @Label(content="Birth day")
     * @var string
     */
    protected $birthDay;

    /**
     * Active
     *
     * @var boolean
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
