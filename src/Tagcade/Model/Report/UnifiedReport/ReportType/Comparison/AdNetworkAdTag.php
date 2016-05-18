<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkAdTagReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkAdTag as UnifiedAdNetworkAdTagReportType;

class AdNetworkAdTag extends UnifiedAdNetworkAdTagReportType
{
    const REPORT_TYPE = 'unified.comparison.network.adtag';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkAdTagReportInterface;
    }
}