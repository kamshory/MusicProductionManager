<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\UserType;


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
     * @var boolean
     * @Column(name=default_data)
     */
    protected $defaultData;

    /**
     * Active
     *
     * @var boolean
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
