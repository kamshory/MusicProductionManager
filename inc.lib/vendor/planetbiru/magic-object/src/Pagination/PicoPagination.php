<?php
namespace MagicObject\Pagination;

use MagicObject\Exceptions\NullPointerException;

class PicoPagination
{
    /**
     * Current page
     *
     * @var integer
     */
    private $currentPage = 0;

    /**
     * Page size
     *
     * @var integer
     */
    private $pageSize = 0;

    /**
     * Offset
     *
     * @var integer
     */
    private $offset = 0;
    
    /**
     * Order by
     *
     * @var string
     */
    private $orderBy = null;

    /**
     * Order type
     *
     * @var string
     */
    private $orderType = "";

    public function __construct($pageSize = 20)
    {
        $this->pageSize = $pageSize;
        $this->currentPage = $this->parseCurrentPage();
        $this->offset = $this->pageSize * ($this->currentPage - 1);
        $this->orderBy = @$_GET['orderby'];
        $this->orderType = @$_GET['ordertype'];
    }

    /**
     * Convert snake case to camel case
     *
     * @param string $input
     * @param string $separator
     * @return string
     */
    protected function camelize($input, $separator = '_')
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }

    /**
     * Convert camel case to snake case
     *
     * @param string $input
     * @param string $glue
     * @return string
     */
    protected function snakeize($input, $glue = '_') {
        return ltrim(
            preg_replace_callback('/[A-Z]/', function ($matches) use ($glue) {
                return $glue . strtolower($matches[0]);
            }, $input),
            $glue
        );
    } 

    /**
     * Parse offset
     *
     * @return integer
     */
    private function parseCurrentPage()
    {
        if(isset($_GET['page']))
        {
            $pageStr = preg_replace("/\D/", "", $_GET['page']);
            if($pageStr == "")
            {
                $page = 0;
            }
            else
            {
                $page = abs(intval($pageStr));
            }
            if($page < 1)
            {
                $page = 1;
            }
            return $page;
        }
        return 1;
    }

    /**
     * Create order
     *
     * @param array $map
     * @param array $filter
     * @param string $defaultOrderBy
     * @return string
     */
    public function createOrder($map = null, $filter = null, $defaultOrderBy = null)
    {
        $orderBy = $this->getOrderBy($filter, $defaultOrderBy);
        $orderByFixed = $orderBy;
        // mapping if any
        if($map != null && is_array($map) && isset($map[$orderBy]))
        {
            $orderByFixed = $map[$orderBy];
        }
        if($orderByFixed == null)
        {
            throw new NullPointerException("ORDER BY can not be null");
        }
        return $orderByFixed." ".$this->getOrderType();
    }

    /**
     * Get order by
     *
     * @var array $filter
     * @var string $defaultOrderBy
     * @return string
     */ 
    public function getOrderBy($filter = null, $defaultOrderBy = null)
    {
        $orderBy = $this->camelize($this->orderBy);
        if($filter != null && is_array($filter) && !isset($filter[$orderBy]))
        {
            $orderBy = null;
        }
        if(($orderBy == null || empty($this->orderBy)) && $defaultOrderBy != null)
        {
            $orderBy = $this->camelize($defaultOrderBy);
        }
        if(empty($orderBy))
        {
            $orderBy = null;
        }
        return $orderBy;
    }

    /**
     * Get order type
     *
     * @var string $defaultOrderType
     * @return string
     */ 
    public function getOrderType($defaultOrderType = null)
    {
        $orderType = $this->orderType;
        if(strcasecmp($orderType, 'desc') == 0)
        {
            $orderType = 'desc';
        }
        else if(strcasecmp($orderType, 'asc') == 0)
        {
            $orderType = 'asc';
        }
        else
        {
            $orderType = null;
        }
        if($orderType == null && $defaultOrderType != null)
        {
            $orderType = $defaultOrderType;
        }
        return $orderType;
    }   

    /**
     * Get current page
     *
     * @return integer
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
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

    /**
     * Get offset
     *
     * @return integer
     */ 
    public function getOffset()
    {
        return $this->offset;
    }
}