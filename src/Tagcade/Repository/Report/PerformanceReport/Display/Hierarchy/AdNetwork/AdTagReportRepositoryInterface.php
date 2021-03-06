<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReportInterface;

interface AdTagReportRepositoryInterface
{
    /**
     * @param AdTagInterface $adTag
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportFor(AdTagInterface $adTag, DateTime $startDate, DateTime $endDate);

    public function overrideReport(AdTagReportInterface $report);
}