<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\Genre;


/**
 * Genre DTO
 * @JSON (property-naming-strategy=SNAKE_CASE)
 */
class GenreDto extends SetterGetter
{
    /**
     * Genre ID
     * 
     * @Label(content="Genre ID")
     * @var string
     */
    protected $genreId;

    /**
     * Title
     * 
     * @Label(content="Title")
     * @var string
     */
    protected $name;

    /**
     * Sort order
     * 
     * @Label(content="Sort order")
     * @var integer
     * @Column(name=sort_order)
     */
    protected $sortOrder;

    /**
     * Default data
     * 
     * @Label(content="Default data")
     * @var bool
     * @Column(name=default_data)
     */
    protected $defaultData;

    /**
     * Admin
     * 
     * @Label(content="Admin")
     * @var bool
     */
    protected $admin;

    /**
     * Active
     * 
     * @Label(content="Active")
     * @var bool
     */
    protected $active;

    /**
     * Construct GenreDto from Genre and not copy other properties
     * 
     * @param Genre $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new GenreDto();
        $output->setGenreId($input->getGenreId());
        $output->setName($input->getName());
        $output->setSortOrder($input->getSortOrder());
        $output->setDefaultData($input->getDefaultData());
        $output->setAdmin($input->getAdmin());
        $output->setActive($input->getActive());
        return $output;
    }
}
