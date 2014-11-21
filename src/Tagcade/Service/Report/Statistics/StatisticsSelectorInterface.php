<?php

namespace Tagcade\Service\Report\Statistics;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use DateTime;

interface StatisticsSelectorInterface
{
    /**
     * @param StatisticsTypeInterface $selectorType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $deepLength
     * @return array
     */
    public function getStatistics(StatisticsTypeInterface $selectorType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = 10);

}