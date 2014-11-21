<?php

namespace Tagcade\Domain\DTO\Report\Statistics;

use DateTime;
use Tagcade\Service\Report\Statistics\Selector\AccountStatistics;

class PlatformStatisticsDTO implements StatisticsInterface
{
    /**
     * @var DateTime
     */
    protected $startDate;

    /**
     * @var DateTime
     */
    protected $endDate;

    protected $totalBillingCost;

    protected $totalOpportunities;

    /**
     * @var AccountStatistics[]
     */
    protected $topAccounts;

    function __construct(DateTime $startDate, DateTime $endDate, $totalBillingCost, $totalOpportunities, array $topAccounts)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalBillingCost = $totalBillingCost;
        $this->totalOpportunities = $totalOpportunities;
        $this->topAccounts = $topAccounts;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return float
     */
    public function getTotalBillingCost()
    {
        return $this->$totalBillingCost;
    }

    /**
     * @return int
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @return array
     */
    public function getTopAccounts()
    {
        return $this->topAccounts;
    }
} 