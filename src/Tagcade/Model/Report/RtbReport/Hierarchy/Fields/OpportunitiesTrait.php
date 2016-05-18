<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy\Fields;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRatiosTrait;

trait OpportunitiesTrait
{
    use CalculateRatiosTrait;

    /**
     * @inheritdoc
     */
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @inheritdoc
     */
    public function setOpportunity($opportunities)
    {
        $this->opportunities = (int)$opportunities;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getOpportunities() === null) {
            throw new RuntimeException('slot opportunities must be defined to calculate fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports except for AdTagReport
        return $this->getPercentage($this->getImpressions(), $this->getOpportunities());
    }

    /**
     * @return int|null
     */
    abstract public function getImpressions();
} 