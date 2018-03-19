<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;

interface CpmRateGetterInterface
{
    /**
     * @param int $slotOpportunities
     * @param $module
     * @param BillingConfigurationInterface $billingConfiguration
     * @return float
     */
    public function getDefaultCpmRate($slotOpportunities, $module, BillingConfigurationInterface $billingConfiguration = null);

    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param DateTime $date
     * @return float threshold rate of current publisher on a specific month
     */
    public function getCpmRateForPublisherByMonth(PublisherInterface $publisher, $module, DateTime $date);

    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param int $weight
     * @return CpmRate
     */
    public function getCpmRateForPublisher(PublisherInterface $publisher, $module, $weight);

    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @param DateTime $date
     * @return mixed
     */
    public function getBillingWeightForPublisherInMonthBeforeDate(PublisherInterface $publisher, $module, DateTime $date);
}