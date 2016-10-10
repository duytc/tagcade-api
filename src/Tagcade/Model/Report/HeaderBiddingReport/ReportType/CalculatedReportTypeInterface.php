<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType;

use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

interface CalculatedReportTypeInterface extends ReportTypeInterface
{
    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report);
}