<?php

namespace Pico\Data\Dto;

use Pico\Data\Entity\UserType;
use Pico\DynamicObject\SetterGetter;

/**
 * User Type DTO
 * @JSON (property-naming-strategy=SNAKE_CASE)
 */
class UserTypeDto extends SetterGetter
{
    /**
     * User Type ID
     *
     * @var string
     */
    protected $userTypeId;

    /**
     * Name
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
     * Construct UserTypeDto from UserType and not copy other properties
     *
     * @param UserType $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new UserTypeDto();
        $output->setUserTypeId($input->getUserTypeId());
        $output->setName($input->getName());
        $output->setSortOrder($input->getSortOrder());
        $output->setDefaultData($input->getDefaultData());
        $output->setActive($input->getActive());        
        return $output;
    } 
}
