<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkDomainAdTag as UnifiedAdNetworkDomainAdTagReportType;

class AdNetworkDomainAdTag extends UnifiedAdNetworkDomainAdTagReportType
{
    const REPORT_TYPE = 'unified.comparison.network.domain_adtag';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkDomainAdTagReportInterface;
    }
}