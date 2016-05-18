<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType;


use Tagcade\Model\Report\UnifiedReport\ReportInterface;

interface ReportTypePartnerInterface
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