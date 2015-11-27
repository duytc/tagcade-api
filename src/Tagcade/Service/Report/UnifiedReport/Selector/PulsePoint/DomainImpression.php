<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\SelectorInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DomainImpression implements SelectorInterface
{
    public function supportReport(ReportTypeInterface $reportType)
    {
        // TODO: Implement supportReport() method.
    }

    public function getReports(ReportTypeInterface $reportType, UnifiedReportParams $params)
    {
        // TODO: Implement getReports() method.
    }
}