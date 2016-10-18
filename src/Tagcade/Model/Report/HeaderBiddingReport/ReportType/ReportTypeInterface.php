<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType;

use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

interface ReportTypeInterface
{
    /**
     * Checks if the report is a valid report for this report type
     *
     * @param ReportInterface $report
     * @return bool
     */
    public function matchesReport(ReportInterface $report);
}