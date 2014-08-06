<?php

namespace Tagcade\Model\Report\Behaviors;

use DateTime;

trait CalculatedDisplayReport
{
    use GenericReport;
    use CalculateRatios;

    protected $id;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $slotOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $subReports = [];

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalOpportunities()
    {
        return $this->totalOpportunities;
    }

    /**
     * @param int $totalOpportunities
     * @return self
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
     */
    public function setSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities = $slotOpportunities;
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
     * @return self
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
     * @return self;
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
     * @return $this
     */
    protected function setFillRate()
    {
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
        return $this;
    }
}