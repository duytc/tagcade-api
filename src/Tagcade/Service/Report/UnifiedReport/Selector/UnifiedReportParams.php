<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;


use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

class UnifiedReportParams extends Params
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $size;
    /**
     * @var string
     */
    private $searchField;
    /**
     * @var string
     */
    private $searchKey;
    /**
     * @var string
     */
    private $sortField;
    /**
     * @var string
     */
    private $sortDirection;

    /**
     * @param DateTime $startDate
     * @param DateTime|null $endDate
     * @param bool $group
     * @param int $page
     * @param int $size
     * @param string $searchField
     * @param string $searchKey
     * @param string $sortField
     * @param string $sortDirection
     */
    function __construct(DateTime $startDate, DateTime $endDate = null, $group = false, $page = 1, $size = 10, $searchField, $searchKey, $sortField, $sortDirection)
    {
        parent::__construct($startDate, $endDate, false, $group);

        $this->page = $page;
        $this->size = $size;
        $this->searchField = $searchField;
        $this->searchKey = $searchKey;
        $this->sortField = $sortField;
        $this->sortDirection = $sortDirection;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSearchField()
    {
        return $this->searchField;
    }

    /**
     * @param string $searchField
     */
    public function setSearchField($searchField)
    {
        $this->searchField = $searchField;
    }

    /**
     * @return string
     */
    public function getSearchKey()
    {
        return $this->searchKey;
    }

    /**
     * @param string $searchKey
     */
    public function setSearchKey($searchKey)
    {
        $this->searchKey = $searchKey;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @param string $sortField
     */
    public function setSortField($sortField)
    {
        $this->sortField = $sortField;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;
    }
}