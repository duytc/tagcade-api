<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;


use Tagcade\Model\User\Role\PublisherInterface;

interface ProjectedBillingCalculatorInterface
{

    /**
     * This will do calculation of projected billed amount for current month
     * @param PublisherInterface $publisher
     * @return float|bool projected billed amount or false on failure
     */
    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher);

}