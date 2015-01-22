<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;

class BillingCalculator implements BillingCalculatorInterface
{
    use CalculateBilledAmountTrait;
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
}