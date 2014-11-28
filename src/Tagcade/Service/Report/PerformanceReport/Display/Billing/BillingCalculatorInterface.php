<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Model\User\Role\PublisherInterface;

interface BillingCalculatorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int $slotOpportunities
     * @return float
     */
    public function calculateBilledAmountForPublisher(PublisherInterface $publisher, $slotOpportunities);
}