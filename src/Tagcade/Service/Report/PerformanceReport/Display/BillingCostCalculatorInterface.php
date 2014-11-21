<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Tagcade\Model\User\UserEntityInterface;

interface BillingCostCalculatorInterface
{
    /**
     * @param UserEntityInterface $publisher
     * @param $totalOpportunities
     * @return float
     */
    public function calculateCostByAdTag(UserEntityInterface $publisher, $totalOpportunities);
}