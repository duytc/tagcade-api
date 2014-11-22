<?php

namespace Tagcade\Domain\DTO\Report\PerformanceReport\Display;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use DateTime;

class ExpandedReportCollection extends ReportCollection
{
    /**
     * @var ReportInterface[]
     */
    protected $expandedReports;

    /**
     * @param ReportTypeInterface $reportType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string name
     * @param ReportInterface[] $reports
     * @param ReportInterface[] $expandedReports
     */
    public function __construct(ReportTypeInterface $reportType, DateTime $startDate, DateTime $endDate, $name, array $reports, array $expandedReports)
    {
        parent::__construct($reportType, $startDate, $endDate, $name, $reports);
        $this->expandedReports = $expandedReports;
    }

    /**
     * @return ReportInterface[]
     */
    public function getExpandedReports()
    {
        return $this->expandedReports;
    }
}