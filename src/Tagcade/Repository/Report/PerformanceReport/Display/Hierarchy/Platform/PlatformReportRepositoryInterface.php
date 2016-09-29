<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;

interface PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate);

    public function getSumBilledAmountForDateRange(DateTime $startDate, DateTime $endDate);

    public function getStatsSummaryForDateRange(DateTime $startDate, DateTime $endDate);

    public function overrideReport(PlatformReportInterface $report);
}