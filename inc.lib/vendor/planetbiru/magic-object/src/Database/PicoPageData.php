<?php

namespace MagicObject\Database;

use MagicObject\MagicObject;
use stdClass;

class PicoPageData
{
    const RESULT = 'result';
    const PAGABLE = 'pageable';

    /**
     * Result
     *
     * @var MagicObject[]
     */
    private $result = array();

    /**
     * Pagable
     *
     * @var PicoPageable
     */
    private $pageable;

    /**
     * Total match
     *
     * @var integer
     */
    private $totalResult = 0;

    /**
     * Total page
     *
     * @var integer
     */
    private $totalPage = 0;
    /**
     * Page number
     * @var integer
     */
    private $pageNumber = 0;

    /**
     * Page size
     * @var integer
     */
    private $pageSize = 0;
    
    /**
     * Start time
     *
     * @var float
     */
    private $startTime = 0.0;
    
    /**
     * End time
     *
     * @var float
     */
    private $endTime = 0.0;
    
    /**
     * Execution time
     *
     * @var float
     */
    private $executionTime = 0.0;

    /**
     * Pagination
     *
     * @var array
     */
    private $pagination = array();

    /**
     * Constructor
     *
     * @param MagicObject[] $result
     * @param integer $startTime
     * @param integer $totalResult
     * @param PicoPageable $pageable
     */
    public function __construct($result, $startTime, $totalResult = 0, $pageable = null)
    {
        $this->startTime = $startTime;
        $this->result = $result;
        $countResult = count($result);
        if($totalResult != 0)
        {
            $this->totalResult = $totalResult;
        }
        else
        {
            $this->totalResult = $countResult;
        }
        if($pageable != null && $pageable instanceof PicoPageable)
        {
            $this->pageable = $pageable;
            $this->calculateContent();
        }
        else
        {
            $this->pageNumber = 1;
            $this->totalPage = 1;
            $this->pageSize = $countResult;
        }
        $this->endTime = microtime(true);
        $this->executionTime = $this->endTime - $this->startTime;
    }


    /**
     * Calculate content
     *
     * @return void
     */
    private function calculateContent()
    {
        $this->pageNumber = $this->pageable->getPage()->getPageNumber();
        $this->totalPage = ceil($this->totalResult / $this->pageable->getPage()->getPageSize());
        $this->pageSize = $this->pageable->getPage()->getPageSize();

        $curPage = $this->pageNumber;
        $totalPage = $this->totalPage;

        $minPage = $curPage - 3;
        if($minPage < 1)
        {
            $minPage = 1;
        }
        $maxPage = $curPage + 3;
        if($maxPage > $totalPage)
        {
            $maxPage = $totalPage;
        }
        $this->pagination = array();
        for($i = $minPage; $i<=$maxPage; $i++)
        {
            $this->pagination[] = array('page'=>$i, 'selected'=>$i == $curPage);
        }
    }

    /**
     * Get result
     *
     * @return MagicObject[]
     */ 
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get page number
     *
     * @return integer
     */ 
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * Get total page
     *
     * @return integer
     */ 
    public function getTotalPage()
    {
        return $this->totalPage;
    }

    /**
     * Get page size
     *
     * @return integer
     */ 
    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function __toString()
    {
        $obj = new stdClass;
        foreach($this as $key=>$value)
        {
            if($key != self::RESULT && $key != self::PAGABLE)
            {
                $obj->{$key} = $value;
            }
        }
        return json_encode($obj);
    }

    /**
     * Get execution time
     *
     * @return float
     */ 
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    /**
     * Get the value of pagination
     */ 
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Get total match
     *
     * @return integer
     */ 
    public function getTotalResult()
    {
        return $this->totalResult;
    }

    /**
     * Get pageable
     *
     * @return PicoPageable
     */ 
    public function getPagable()
    {
        return $this->pageable;
    }
}