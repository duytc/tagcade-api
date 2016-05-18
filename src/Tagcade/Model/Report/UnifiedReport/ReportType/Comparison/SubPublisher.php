<?php


namespace Tagcade\Model\Report\UnifiedReport\ReportType\Comparison;


use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherReportInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher\SubPublisher as UnifiedSubPublisherReportType;

class SubPublisher extends UnifiedSubPublisherReportType
{
    const REPORT_TYPE = 'unified.comparison.subPublisher';

    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof SubPublisherReportInterface;
    }
}