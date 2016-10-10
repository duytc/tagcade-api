<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Result;

use ArrayIterator;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportDataInterface;
use DateTime;

class ReportCollection implements ReportResultInterface
{
    protected $reportType;
    protected $startDate;
    protected $endDate;
    protected $reports;
    protected $name;

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportDataInterface[] $reports
     * @param $name
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name = null)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
        $this->name = $name;
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
        return $this->reports;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }
}