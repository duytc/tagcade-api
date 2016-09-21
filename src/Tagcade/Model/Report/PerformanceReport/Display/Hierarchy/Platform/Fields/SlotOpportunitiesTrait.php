<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRatiosTrait;

trait SlotOpportunitiesTrait
{
    use CalculateRatiosTrait;

    protected $slotOpportunities;

    /**
     * @var float
     */
    protected $billedAmount;

    /**
     * @var float
     */
    protected $billedRate;

    protected $rtbImpressions;

    /**
     * @inheritdoc
     */
    public function getSlotOpportunities()
    {
        return $this->slotOpportunities;
    }

    /**
     * @inheritdoc
     */
    public function setSlotOpportunities($slotOpportunities)
    {
        $this->slotOpportunities = (int) $slotOpportunities;

        return $this;
    }

    /**
     * @param $rtbImpressions
     * @return $this
     */
    public function setRtbImpressions($rtbImpressions)
    {
        $this->rtbImpressions = (int)$rtbImpressions;

        return $this;
    }

    /**
     * @return int
     */
    public function getRtbImpressions()
    {
        return (int)$this->rtbImpressions;
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @param float $billedAmount
     * @return $this
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getBilledRate()
    {
        return $this->billedRate;
    }

    /**
     * @param float $billedRate
     */
    public function setBilledRate($billedRate)
    {
        $this->billedRate = $billedRate;
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getSlotOpportunities() === null) {
            throw new RuntimeException('slot opportunities must be defined to calculate fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports except for WaterfallTagReport
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @return int|null
     */
    abstract public function getImpressions();
}