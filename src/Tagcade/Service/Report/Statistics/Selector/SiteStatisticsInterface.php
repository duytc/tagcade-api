<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use DateTime;
use Tagcade\Service\Report\Statistics\StatisticsInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountType;

interface SiteStatisticsInterface extends StatisticsInterface {

    /**
     * @param AccountType $accountType
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return SiteStatisticsInterface[]
     */
    public function getStatisticsForAllSites(AccountType $accountType, DateTime $startDate = null, DateTime $endDate = null);
} 