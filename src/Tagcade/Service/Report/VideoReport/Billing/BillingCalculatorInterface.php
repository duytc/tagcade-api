<?php


namespace Tagcade\Service\Report\VideoReport\Billing;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\RateAmount;

interface BillingCalculatorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param $newWeight
     * @return RateAmount
     */
    public function calculateTodayBilledAmountForPublisher(PublisherInterface $publisher, $module, $newWeight);
}