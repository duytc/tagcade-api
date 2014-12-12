<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class BillingCalculator implements BillingCalculatorInterface
{
    /**
     * @var CpmRateGetterInterface
     */
    private $defaultRateGetter;

    function __construct(CpmRateGetterInterface $defaultRateGetter)
    {
        $this->defaultRateGetter = $defaultRateGetter;
    }

    /**
     * @inheritdoc
     */
    public function calculateBilledAmountForPublisher(PublisherInterface $publisher, $slotOpportunities)
    {
        if (!is_int($slotOpportunities)) {
            throw new InvalidArgumentException('Slot opportunities must be a number');
        }

        $cpmRate = $this->defaultRateGetter->getBilledRateForPublisher($publisher);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate, $slotOpportunities));
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