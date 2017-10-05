<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\Fields;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRatiosTrait;

trait SlotOpportunitiesTrait
{
    use CalculateRatiosTrait;

    /**
     * @var int
     */
    protected $slotOpportunities;

    /**
     * @var float
     */
    protected $opportunityFillRate; // calculated base on slotOpportunities

    /**
     * @var float
     */
    protected $billedAmount;

    /**
     * @var float
     */
    protected $billedRate;

    // TODO: all following inbanner should be move to other trait. Or rename this trait to real it's mean
    // current trait is for slot opportunities only

    /**
     * @var float
     */
    protected $inBannerBilledRate;

    /**
     * @var float
     */
    protected $inBannerBilledAmount;

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
        $this->slotOpportunities = (int)$slotOpportunities;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOpportunityFillRate()
    {
        return $this->opportunityFillRate;
    }

    /**
     * @inheritdoc
     */
    public function setOpportunityFillRate($opportunityFillRate)
    {
        $this->opportunityFillRate = (float)$opportunityFillRate;

        return $this;
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
        $this->billedAmount = (float)$billedAmount;

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
        $this->billedRate = (float)$billedRate;
    }

    /**
     * @return float
     */
    public function getInBannerBilledRate()
    {
        return $this->inBannerBilledRate;
    }

    /**
     * @param float $inBannerBilledRate
     * @return self
     */
    public function setInBannerBilledRate($inBannerBilledRate)
    {
        $this->inBannerBilledRate = (float)$inBannerBilledRate;
        return $this;
    }

    /**
     * @return float
     */
    public function getInBannerBilledAmount()
    {
        return $this->inBannerBilledAmount;
    }

    /**
     * @param float $inBannerBilledAmount
     * @return self
     */
    public function setInBannerBilledAmount($inBannerBilledAmount)
    {
        $this->inBannerBilledAmount = (float)$inBannerBilledAmount;
        return $this;
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