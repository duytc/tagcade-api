<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network\NetworkSubPublisher as UnifiedAdNetworkSubPublisherReportType;

class AdNetworkSubPublisher extends UnifiedAdNetworkSubPublisherReportType
{
    const REPORT_TYPE = 'unified.comparison.adNetworkSubPublisher';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherAdNetworkReportInterface;
    }
}