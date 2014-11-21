<?php

namespace Tagcade\Domain\DTO\Report\Statistics;

use DateTime;

class SitesStatisticsDTO implements StatisticsInterface
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

    function __construct(DateTime $startDate, DateTime $endDate, $totalBillingCost, $totalOpportunities)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalBillingCost = $totalBillingCost;
        $this->totalOpportunities = $totalOpportunities;
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

} 