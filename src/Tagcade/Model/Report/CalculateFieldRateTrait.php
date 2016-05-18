<?php

namespace Tagcade\Model\Report;


trait CalculateFieldRateTrait {
    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getTotalOpportunities() === null) {
            throw new \RuntimeException('total opportunities must be defined to calculate ad tag fill rates');
        }

        // note that we use slot opportunities to calculate fill rate in this Reports
        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    abstract protected function getPercentage($numerator, $denominator);

    abstract public function getImpressions();

    abstract public function getTotalOpportunities();

} 