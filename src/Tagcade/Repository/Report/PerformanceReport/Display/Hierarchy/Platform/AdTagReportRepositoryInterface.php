<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportInterface;

interface AdTagReportRepositoryInterface
{
    public function getReportFor(AdtagInterface $adTag, DateTime $startDate, DateTime $endDate);

    public function overrideReport(AdTagReportInterface $report);
}