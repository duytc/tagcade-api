<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkDomainAdTagSubPublisher as UnifiedAdNetworkDomainAdTagSubPublisherReportType;

class AdNetworkDomainAdTagSubPublisher extends UnifiedAdNetworkDomainAdTagSubPublisherReportType
{
    const REPORT_TYPE = 'unified.comparison.network.domain_adtag_subPublisher';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkDomainAdTagSubPublisherReportInterface;
    }
}