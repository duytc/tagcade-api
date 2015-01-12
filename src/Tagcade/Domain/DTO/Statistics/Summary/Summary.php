<?php

namespace Tagcade\Domain\DTO\Statistics\Summary;


class Summary {

    private $totalSlotOpportunities;
    private $totalOpportunities;
    private $billedAmount;
    private $revenue;
    private $impressions;

    function __construct($totalSlotOpportunities, $totalOpportunities, $impressions, $billedAmount, $revenue)
    {
        $this->totalSlotOpportunities = $totalSlotOpportunities;
        $this->totalOpportunities = $totalOpportunities;
        $this->impressions = $impressions;
        $this->billedAmount = $billedAmount;
        $this->revenue = $revenue;
    }

    /**
     * @return mixed
     */
    public function getTotalSlotOpportunities()
    {
        return $this->totalSlotOpportunities;
    }

    /**
     * @return mixed
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }


    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }


}