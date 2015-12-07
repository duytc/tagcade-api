<?php

namespace Tagcade\Service\Report\UnifiedReport\Result;

use ArrayIterator;
use DateTime;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;

class ReportCollection implements ReportResultInterface
{
    protected $reportType;
    protected $startDate;
    protected $endDate;
    protected $pagination;
    protected $name;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param SlidingPagination $pagination
     * @param string $name
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, SlidingPagination $pagination, $name = null)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->pagination = $pagination;
    }

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return ReportDataInterface[]
     */
    public function getReports()
    {
        return $this->pagination->getItems();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->getReports());
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalRecord()
    {
        $this->pagination->getTotalItemCount();
    }

    /**
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}