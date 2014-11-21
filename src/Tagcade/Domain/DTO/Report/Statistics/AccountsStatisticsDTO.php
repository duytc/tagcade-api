<?php

namespace Tagcade\Domain\DTO\Report\Statistics;

use DateTime;
use Tagcade\Service\Report\Statistics\Selector\SiteStatistics;

class AccountsStatisticsDTO implements StatisticsInterface
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

    protected $publisher;

    /**
     * @var SiteStatistics[]
     */
    protected $topSites;

    function __construct(DateTime $startDate, DateTime $endDate, $totalBillingCost, $totalOpportunities, $publisher, array $topSites)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalBillingCost = $totalBillingCost;
        $this->totalOpportunities = $totalOpportunities;
        $this->publisher = $publisher;
        $this->topSites = $topSites;
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
        return $this->totalBillingCost;
    }

    /**
     * @return int
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @return object
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return array
     */
    public function getTopSites()
    {
        return $this->topSites;
    }
} 