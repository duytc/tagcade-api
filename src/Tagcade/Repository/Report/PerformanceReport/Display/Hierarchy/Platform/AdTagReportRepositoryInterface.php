<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportInterface;

interface AdTagReportRepositoryInterface
{
    /**
     * @param AdTagInterface $adTag
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getReportFor(AdTagInterface $adTag, DateTime $startDate, DateTime $endDate);

    /**
     * @param AdTagReportInterface $report
     * @return mixed
     */
    public function overrideReport(AdTagReportInterface $report);
}