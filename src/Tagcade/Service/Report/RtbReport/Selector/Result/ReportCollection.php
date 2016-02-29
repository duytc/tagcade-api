<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Result;

use ArrayIterator;
use DateTime;
use Tagcade\Model\Report\RtbReport\ReportDataInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

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
     * @param string $name
     */
    public function __construct($reportType, DateTime $startDate, DateTime $endDate, array $reports, $name = null)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->reports = $reports;
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

    public function getIterator()
    {
        return new ArrayIterator($this->reports);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
}