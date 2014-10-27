<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    const REPORT_TYPE = 'platform.platform';

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    protected function setDefaultName()
    {
        // do nothing, a name isn't needed for this report
    }
}