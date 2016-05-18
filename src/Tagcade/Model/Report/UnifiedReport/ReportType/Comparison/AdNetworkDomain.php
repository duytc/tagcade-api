<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkSite as UnifiedAdNetworkDomainReportType;

class AdNetworkDomain extends UnifiedAdNetworkDomainReportType
{
    const REPORT_TYPE = 'unified.comparison.network.domain';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkDomainReportInterface;
    }
}