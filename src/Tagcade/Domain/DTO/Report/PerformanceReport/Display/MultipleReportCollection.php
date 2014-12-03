<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use DateTime;

class MultipleReportCollection
{
    protected $reportTypes;
    protected $startDate;
    protected $endDate;
    protected $reports;

    /**
     * @param ReportTypeInterface[] $reportTypes
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param ReportInterface[] $reports
     */
    public function __construct(array $reportTypes, DateTime $startDate, DateTime $endDate, array $reports)
    {
        $this->reportTypes = $reportTypes;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reports = $reports;
    }

    /**
     * @return ReportTypeInterface[]
     */
    public function getReportTypes()
    {
        return $this->reportTypes;
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
     * @return ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }
}