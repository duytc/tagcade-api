<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }
}