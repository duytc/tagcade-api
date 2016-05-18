<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\Network as UnifiedAdNetworkReportType;

class AdNetwork extends UnifiedAdNetworkReportType
{
    const REPORT_TYPE = 'unified.comparison.network';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkReportInterface;
    }
}