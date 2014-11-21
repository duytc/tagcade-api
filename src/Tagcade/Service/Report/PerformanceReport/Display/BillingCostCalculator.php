<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use Doctrine\ORM\NoResultException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\UserEntityInterface;

class BillingCostCalculator implements BillingCostCalculatorInterface
{
    /**
     * @var int
     */
    protected $defaultBillingRate;

    public function __construct($defaultBillingRate)
    {
        if(!is_numeric($defaultBillingRate) || $defaultBillingRate < 0) {
            throw new InvalidArgumentException('Default billing rate must be numeric and positive');
        }

        $this->defaultBillingRate = $defaultBillingRate;
    }

    /**
     * @inheritdoc
     */
    public function calculateCostByAdTag(UserEntityInterface $publisher, $totalOpportunities)
    {
        try {
            $billingRate = $publisher->getBillingRate();
        }
        catch(NoResultException $ex) {
            $billingRate = $this->defaultBillingRate;
        }

        return $billingRate * $totalOpportunities;
    }
}