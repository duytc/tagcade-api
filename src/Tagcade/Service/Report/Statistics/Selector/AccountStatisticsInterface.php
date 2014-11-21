<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use DateTime;
use Tagcade\Service\Report\Statistics\StatisticsInterface;

interface AccountStatisticsInterface extends StatisticsInterface
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return AccountStatisticsInterface[]
     */
    public function getStatisticsForAllAccounts(DateTime $startDate = null, DateTime $endDate = null);
}