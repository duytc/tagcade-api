<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface CalculatedReportTypeInterface extends ReportTypeInterface
{
    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report);
}