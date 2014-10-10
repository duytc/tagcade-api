<?php

namespace Tagcade\Model\Report\PerformanceReport\Behaviors;

use Tagcade\Model\Report\Behaviors\CalculateRatios;

trait CalculatedDisplayReport
{
    use GenericReport;
    use CalculateRatios;

    protected $totalOpportunities;
    protected $slotOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;

    /**
     * @return int|null
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @param int $totalOpportunities
     * @return static
     */
    public function setTotalOpportunities($totalOpportunities)
    {
        $this->totalOpportunities = $totalOpportunities;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @param int $slotOpportunities
     * @return static
     */
    public function setSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities = $slotOpportunities;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param int $impressions
     * @return static
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @param int $passbacks
     * @return static;
     */
    public function setPassbacks($passbacks)
    {
        $this->passbacks = $passbacks;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @return static
     */
    protected function setFillRate()
    {
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
        return $this;
    }
}