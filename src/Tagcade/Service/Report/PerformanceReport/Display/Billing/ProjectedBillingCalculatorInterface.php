<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;


use Tagcade\Model\User\Role\PublisherInterface;

interface ProjectedBillingCalculatorInterface
{

    /**
     * This will do calculation of projected billed amount for entire month
     * @param PublisherInterface $publisher
     * @return RateAmount
     */
    public function calculateProjectedBilledAmountForPublisher(PublisherInterface $publisher);

    /**
     * @return RateAmount[]
     */
    public function calculateProjectedBilledAmountForAllPublishers();
}