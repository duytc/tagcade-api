<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\AbstractCalculatedReport;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }
}