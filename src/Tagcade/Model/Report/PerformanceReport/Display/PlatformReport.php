<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

class PlatformReport extends AbstractCalculatedReport
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }
}