<?php

namespace Tagcade\Domain\DTO\Statistics\Summary;


class Summary {

    private $totalSlotOpportunities;
    private $totalOpportunities;
    private $billedAmount;
    private $revenue;

    function __construct($totalSlotOpportunities, $totalOpportunities, $billedAmount, $revenue)
    {
        $this->totalSlotOpportunities = $totalSlotOpportunities;
        $this->totalOpportunities = $totalOpportunities;
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