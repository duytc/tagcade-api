<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class BillingCalculator implements BillingCalculatorInterface
{
    /**
     * @var CpmRateGetterInterface
     */
    private $cpmRateGetter;

    function __construct(CpmRateGetterInterface $defaultRateGetter)
    {
        $this->cpmRateGetter = $defaultRateGetter;
    }

    public function calculateTodayBilledAmountForPublisher(PublisherInterface $publisher, $slotOpportunities)
    {
        if (!is_int($slotOpportunities) || $slotOpportunities < 0) {
            throw new InvalidArgumentException('Slot opportunities must be a number');
        }

        $cpmRate = $this->cpmRateGetter->getTodayCpmRateForPublisher($publisher);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $slotOpportunities));
    }


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