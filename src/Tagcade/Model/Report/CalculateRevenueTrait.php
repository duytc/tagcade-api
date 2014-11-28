<?php

namespace Tagcade\Model\Report;

use Tagcade\Exception\RuntimeException;

trait CalculateRevenueTrait
{
    /**
     * @param int $impressions
     * @param float $estCpm
     * @return float
     */
    protected function calculateEstRevenue($impressions, $estCpm)
    {
        if ($estCpm === null || $impressions === null) {
            throw new RuntimeException('cannot calculate estRevenue, missing data');
        }

        $estRevenue = $this->getRatio($impressions * $estCpm, 1000);

        if (!$estRevenue) {
            return (float) 0;
        }

        return $estRevenue;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    abstract protected function getRatio($numerator, $denominator);
}