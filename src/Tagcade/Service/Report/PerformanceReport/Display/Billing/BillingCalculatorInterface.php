<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Model\User\Role\PublisherInterface;

interface BillingCalculatorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param $weight
     * @return RateAmount
     */
    public function calculateTodayBilledAmountForPublisher(PublisherInterface $publisher, $module, $weight);

    /**
     * @param float $cpmRate
     * @param int $slotOpportunities
     * @return float
     */
    public function calculateBilledAmount($cpmRate, $slotOpportunities);
}