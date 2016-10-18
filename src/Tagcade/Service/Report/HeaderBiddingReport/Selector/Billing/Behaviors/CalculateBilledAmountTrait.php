<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Billing\Behaviors;


use Tagcade\Exception\InvalidArgumentException;

trait CalculateBilledAmountTrait {

    /**
     * @param float $cpmRate
     * @param int $slotOpportunities
     * @return float
     */
    public function calculateBilledAmount($cpmRate, $slotOpportunities)
    {
        if (!is_numeric($cpmRate)) {
            throw new InvalidArgumentException('cpmRate must be a number');
        }

        return (float) ($cpmRate * $slotOpportunities) / 1000;
    }
} 