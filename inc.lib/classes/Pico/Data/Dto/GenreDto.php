<?php

namespace Pico\Data\Dto;

use Pico\Data\Entity\Genre;
use Pico\DynamicObject\SetterGetter;

/**
 * Genre DTO
 * @JSON (property-naming-strategy=SNAKE_CASE)
 */
class GenreDto extends SetterGetter
{
    /**
     * Genre ID
     *
     * @var string
     */
    protected $genreId;

    /**
     * Title
     *
     * @var string
     */
    protected $name;

    /**
     * Sort order
     *
     * @var integer
     * @Column(name=sort_order)
     */
    protected $sortOrder;

    /**
     * Default data
     *
     * @var bool
     * @Column(name=default_data)
     */
    protected $defaultData;

    /**
     * Active
     *
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
        $output->setActive($input->getActive());        
        return $output;
    } 
}
