<?php

namespace MagicObject\Database;

class PicoSort
{
    const ORDER_TYPE_ASC = "asc";
    const ORDER_TYPE_DESC = "desc";
    /**
     * Sort by
     *
     * @var string
     */
    private $sortBy = "";
    
    /**
     * Sort type
     *
     * @var string
     */
    private $sortType = "";
    
    public function __construct($sortBy, $sortType)
    {
        $this->setSortBy($sortBy);
        $this->setSortType($sortType);
    }

    /**
     * Get sort by
     *
     * @return string
     */ 
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Set sort by
     *
     * @param string $sortBy  Sort by
     *
     * @return self
     */ 
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * Get sort type
     *
     * @return string
     */ 
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * Set sort type
     *
     * @param string $sortType  Sort type
     *
     * @return self
     */ 
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;

        return $this;
    }
    
    /**
     * This method is for debug purpose only.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(array('sortBy'=>$this->sortBy, 'sortType'=>$this->sortType));
    }
}