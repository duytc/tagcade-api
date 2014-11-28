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
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getSlotOpportunities() === null) {
            throw new RuntimeException('slot opportunities must be defined to calculate fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports except for AdTagReport
        return $this->getPercentage($this->getImpressions(), $this->getSlotOpportunities());
    }

    /**
     * @return int|null
     */
    abstract public function getImpressions();
} 