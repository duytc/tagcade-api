<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use DateTime;

class ReportCollection
{
    /**
     * @var ReportTypeInterface
     */
    protected $reportType;
    /**
     * @var DateTime
     */
    protected $startDate;
    /**
     * @var DateTime
     */
    protected $endDate;

    /**
     * @var ReportInterface[]
     */
    protected $reports;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $name
     * @param ReportInterface[] $reports
     */
    public function __construct(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $name, array $reports)
    {
        $this->reportType = $reportType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->reports = $reports;
    }

    /**
     * @return ReportTypeInterface
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ReportInterface[]
     */
    public function getReports()
    {
        return $this->reports;
    }
}