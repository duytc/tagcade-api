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
    protected $billedAmount;

    /**
     * @var float
     */
    protected $billedRate;

    // TODO: all following inbanner should be move to other trait. Or rename this trait to real it's mean
    // current trait is for slot opportunities only

    /**
     * @var int
     */
    protected $inBannerRequests;

    /**
     * @var int
     */
    protected $inBannerImpressions;

    /**
     * @var int
     */
    protected $inBannerTimeouts;

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
     * @return int
     */
    public function getInBannerRequests()
    {
        return $this->inBannerRequests;
    }

    /**
     * @param int $inBannerRequests
     * @return self
     */
    public function setInBannerRequests($inBannerRequests)
    {
        $this->inBannerRequests = (int)$inBannerRequests;
        return $this;
    }

    /**
     * @return int
     */
    public function getInBannerImpressions()
    {
        return $this->inBannerImpressions;
    }

    /**
     * @param int $inBannerImpressions
     * @return self
     */
    public function setInBannerImpressions($inBannerImpressions)
    {
        $this->inBannerImpressions = (int)$inBannerImpressions;
        return $this;
    }

    /**
     * @return int
     */
    public function getInBannerTimeouts()
    {
        return $this->inBannerTimeouts;
    }

    /**
     * @param int $inBannerTimeouts
     * @return self
     */
    public function setInBannerTimeouts($inBannerTimeouts)
    {
        $this->inBannerTimeouts = (int)$inBannerTimeouts;
        return $this;
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