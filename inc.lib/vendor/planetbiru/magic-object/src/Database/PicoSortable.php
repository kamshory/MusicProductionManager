<?php

namespace MagicObject\Database;

class PicoSortable
{
    /**
     * Sortable
     *
     * @var PicoSort[]
     */
    private $sortable = array();
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $argc = func_num_args();
        if($argc > 0)
        {
            $params = array();
            if($argc > 1)
            {
                for($i = 0; $i<$argc; $i++)
                {
                    $params[] = func_get_arg($i);
                }
            }
            $this->initSortable($argc, $params);
        }
    }
    /**
     * Initialize sortable
     *
     * @param integer $argc
     * @param array $params
     * @return void
     */
    private function initSortable($argc, $params)
    {
        $this->sortable = array();
        
        $index = 0;
        $sortBy = null;
        foreach($params as $idx=>$value)
        {
            $sortType = null;
            if($idx % 2 == 0)
            {
                $index = (int) ($idx / 2);
                $sortBy = $value;
            }
            else
            {
                $sortType = $value;
                $this->sortable[$index] = $this->createSortable($sortBy, $sortType);
            }
        }
        if($argc % 2 == 1)
        {
            // set order type ASC
            $sortType = PicoSort::ORDER_TYPE_ASC;
            $lastOrder = $this->createSortable($sortBy, $sortType);
            $this->sortable[count($this->sortable) - 1] = $lastOrder;
        }
    }
    
    /**
     * Add sortable
     *
     * @param PicoSort|array $sort
     * @return self
     */
    public function add($sort)
    {
        return $this->addSortable($sort);
    }
    
    /**
     * Add sortable
     *
     * @param PicoSort|array $sort
     * @return self
     */
    public function addSortable($sort)
    {
        if($sort != null)
        {
            if($sort instanceof PicoSort)
            {
                $this->sortable[count($this->sortable)] = $sort;
            }
            else if(is_array($sort))
            {
                $sortable = $this->createSortable($sort[0], $sort[1]);
                $this->sortable[count($this->sortable)] = $sortable;
            }
        }
        return $this;
    }
    
    /**
     * Create sortable
     *
     * @param string $sortBy
     * @param string $sortType
     * @return PicoSort
     */
    public function createSortable($sortBy, $sortType)
    {
        return new PicoSort($sortBy, $sortType);
    }
    
    /**
     * Create sort by
     *
     * @param PicoTableInfo $tableInfo
     * @return string
     */
    public function createOrderBy($tableInfo = null)
    {
        if($this->sortable == null || !is_array($this->sortable) || empty($this->sortable))
        {
            return null;
        }
        $ret = null;
        if($tableInfo == null)
        {
            $ret = $this->createWithoutMapping();
        }
        else
        {
            $ret = $this->createWithMapping($tableInfo);
        }
        return $ret;
    }
    
    /**
     * Create sort without mapping
     *
     * @return string
     */
    private function createWithoutMapping()
    {
        $ret = "";
        if(empty($this->sortable))
        {
            return "";
        }
        $sorts = array();
        foreach($this->sortable as $sortable)
        {
            $columnName = $sortable->getSortBy();
            $sortType = $sortable->getSortType();             
            $sortBy = $columnName;
            $sorts[] = $sortBy . " " . $sortType;           
        }
        if(!empty($sorts))
        {
            $ret = implode(", ", $sorts);
        }
        return $ret;
    }
    
    /**
     * Create sort with mapping
     *
     * @param PicoTableInfo $tableInfo
     * @return string
     */
    private function createWithMapping($tableInfo)
    {
        $ret = null;
        $columns = $tableInfo->getColumns();
        $joinColumns = $tableInfo->getJoinColumns();
        $columnList = array_merge($columns, $joinColumns);
        $columnNames = array();
        foreach($columnList as $column)
        {
            $columnNames[] = $column['name'];
        }
        $sorts = array();
        foreach($this->sortable as $sortable)
        {
            $propertyName = $sortable->getSortBy();
            $sortType = $sortable->getSortType();
            if(isset($columnList[$propertyName]))
            {
                $sortBy = $columnList[$propertyName]['name'];
                $sorts[] = $sortBy . " " . $sortType;
            }
            else if(in_array($propertyName, $columnNames))
            {
                $sorts[] = $propertyName . " " . $sortType;
            }
        }
        if(!empty($sorts))
        {
            $ret = implode(", ", $sorts);
        } 
        return $ret;
    }

    /**
     * Get sortable
     *
     * @return  PicoSort[]
     */ 
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * Get instance of PicoSortable
     *
     * @return PicoSortable
     */
    public static function getInstance()
    {
        return new PicoSortable();
    }
    
    /**
     * This method is for debug purpose only.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->createWithoutMapping();
    }
    
    /**
     * Get sortable from user input
     *
     * @param PicoRequestBase $request
     * @param string[] $map
     * @return PicoSortable
     */
    public static function fromUserInput($request, $map)
    {
        $sortable = new PicoSortable();
        if($map != null && is_array($map))
        {
            foreach($map as $key=>$value)
            {
                if($request->getOrderby() == $key)
                {
                    $sortable->add(new PicoSort($value, PicoSort::fixSortType($request->getOrdertype())));
                }
            }
        }
        return $sortable;
    }
}