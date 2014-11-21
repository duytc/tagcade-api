<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use Tagcade\Domain\DTO\Report\Statistics\StatisticsInterface as DTOStatisticsInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use DateTime;

abstract class AbstractSelector
{
    /**
     * @inheritdoc
     */
    public function getStatistics(StatisticsTypeInterface $statisticsType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = 10)
    {
        return $this->doGetReports($statisticsType, $startDate, $endDate, $deepLength);
    }

    /**
     * @param array $statisticsList
     * @param int $len
     * @return array
     */
    protected function topList(array $statisticsList, $len = 10)
    {
        /**
         * @var DTOStatisticsInterface $statistics
         */
        foreach ($statisticsList as $index => $statistics) {
            $sort[$index]  = $statistics->getTotalBillingCost();
        }

        array_multisort($sort, SORT_DESC, $statisticsList);

        return array_slice($statisticsList, 0, $len);
    }

}