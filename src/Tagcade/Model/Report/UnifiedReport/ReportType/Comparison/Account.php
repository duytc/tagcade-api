<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AccountReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\Publisher as UnifiedAccountReportType;

class Account extends UnifiedAccountReportType
{
    const REPORT_TYPE = 'unified.comparison.account';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }
}