<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface ReportTypeInterface
{
    /**
     * @return string|null
     */
    public function getReportType();

    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report);
}