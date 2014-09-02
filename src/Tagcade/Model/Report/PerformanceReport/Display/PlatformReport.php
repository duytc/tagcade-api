<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    protected function setDefaultName()
    {
        // do nothing, a name isn't needed for this report
    }
}