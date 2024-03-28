<?php

namespace MagicObject\Database;

use MagicObject\MagicObject;
use stdClass;

class PicoPageData
{
    const RESULT = 'result';
    const PAGABLE = 'pagable';

    /**
     * Result
     *
     * @var MagicObject[]
     */
    private $result = array();

    /**
     * Pagable
     *
     * @var PicoPagable
     */
    private $pagable;

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
     * @param PicoPagable $pagable
     * @param integer $match
     */
    public function __construct($result, $pagable, $totalResult, $startTime)
    {
        $this->startTime = $startTime;
        $this->result = $result;
        $this->pagable = $pagable;
        $this->totalResult = $totalResult;
        if($pagable != null && $pagable instanceof PicoPagable)
        {
            $this->calculateContent();
        }
        else
        {
            $this->pageNumber = 1;
            $this->totalPage = 1;
            $this->pageSize = $totalResult;
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
        $this->pageNumber = $this->pagable->getPage()->getPageNumber();
        $this->totalPage = ceil($this->totalResult / $this->pagable->getPage()->getPageSize());
        $this->pageSize = $this->pagable->getPage()->getPageSize();

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
}