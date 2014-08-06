<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

class SiteReport extends AbstractCalculatedReport implements SiteReportInterface
{
    protected $site;

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }
}