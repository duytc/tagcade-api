<?php

namespace Tagcade\Service\Report\Statistics;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;

interface StatisticsInterface
{
    /**
     * @param StatisticsTypeInterface $statisticsType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $deepLength
     * @return Object
     */
    public function getStatistics(StatisticsTypeInterface $statisticsType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = 10);

    /**
     * @param StatisticsTypeInterface $statisticsType
     * @return bool
     */
    public function supportsStatisticsType(StatisticsTypeInterface $statisticsType);

}
