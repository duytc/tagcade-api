<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;

interface PlatformReportRepositoryInterface
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportFor(DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumBilledAmountForDateRange(DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getStatsSummaryForDateRange(DateTime $startDate, DateTime $endDate);

    /**
     * @param PlatformReportInterface $report
     * @return mixed
     */
    public function overrideReport(PlatformReportInterface $report);
}