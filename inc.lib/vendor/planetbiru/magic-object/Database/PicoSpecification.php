<?php

namespace MagicObject\Database;

class PicoSpecification
{
    const LOGIC_AND = "and";
    const LOGIC_OR = "or";

    /**
     * Parent filter logic
     *
     * @var string
     */
    private $parentFilterLogic = null;

    /**
     * PicoPredicate[]
     *
     * @var array
     */
    private $specifications = array();

    /**
     * Add AND specification
     *
     * @param PicoSpecification|PicoPredicate|array $predicate
     * @return self
     */
    public function add($predicate)
    {
        $this->addAnd($predicate);
        return $this;
    }
    
    /**
     * Add AND specification
     *
     * @param PicoSpecification|PicoPredicate|array $predicate
     * @return self
     */
    public function addAnd($predicate)
    {
        if($predicate instanceof PicoPredicate)
        {
            $this->addFilter($predicate, self::LOGIC_AND);
        }
        if($predicate instanceof PicoSpecification)
        {
            $this->addSubfilter($predicate, self::LOGIC_AND);      
        } 
        return $this;
    }

    /**
     * Add OR specification
     *
     * @param PicoSpecification|PicoPredicate|array $predicate
     * @return self
     */
    public function addOr($predicate)
    {
        if($predicate instanceof PicoPredicate)
        {
            $this->addFilter($predicate, self::LOGIC_OR);      
        }  
        if($predicate instanceof PicoSpecification)
        {
            $this->addSubfilter($predicate, self::LOGIC_OR);      
        }  
        return $this;
    }

    /**
     * Add filter
     *
     * @param PicoSpecification|PicoPredicate|array $predicate
     * @param string $logic
     * @return self
     */
    private function addFilter($predicate, $logic)
    {
        if($predicate instanceof PicoPredicate)
        {
            $predicate->setFilterLogic($logic);
            $this->specifications[count($this->specifications)] = $predicate;
        }
        else if(is_array($predicate))
        {
            foreach($predicate as $key=>$value)
            {
                $pred = new PicoPredicate();    
                $pred->equals($key, $value);
                $pred->setFilterLogic($logic);
                $this->specifications[count($this->specifications)] = $pred;
            }
        }
        return $this;
    }

    /**
     * Add subfilter
     *
     * @param PicoSpecification|array $predicate
     * @param string $logic
     * @return self
     */
    private function addSubFilter($predicate, $logic)
    {
        if($predicate instanceof PicoSpecification)
        {
            $specification = new self();
            $specification->setParentFilterLogic($logic);
            $specifications = $predicate->getSpecifications();
            foreach($specifications as $pred)
            {
                $specification->addFilter($pred, $pred->getFilterLogic());
            }
            $this->specifications[count($this->specifications)] = $specification;
        }
        return $this;
    }


    public function isEmpty()
    {
        return empty($this->specifications);
    }


    /**
     * Get predicate
     *
     * @return array
     */ 
    public function getSpecifications()
    {
        return $this->specifications;
    }

    

    /**
     * Get parent filter logic
     *
     * @return string
     */ 
    public function getParentFilterLogic()
    {
        return $this->parentFilterLogic;
    }

    /**
     * Set parent filter logic
     *
     * @param  string  $parentFilterLogic  Parent filter logic
     *
     * @return self
     */ 
    public function setParentFilterLogic($parentFilterLogic)
    {
        $this->parentFilterLogic = $parentFilterLogic;
        return $this;
    }
}