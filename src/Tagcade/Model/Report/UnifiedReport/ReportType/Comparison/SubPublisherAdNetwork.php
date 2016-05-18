<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\SubPublisherNetwork as UnifiedSubPublisherAdNetworkReportType;

class SubPublisherAdNetwork extends UnifiedSubPublisherAdNetworkReportType
{
    const REPORT_TYPE = 'unified.comparison.subPublisherAdNetwork';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherAdNetworkReportInterface;
    }
}