<?php

namespace Tagcade\Service\Report\VideoReport\Billing;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\DataType\CpmRate;

interface CpmRateGetterInterface
{
    /**
     * @param int $slotOpportunities
     * @return float
     */
    public function getDefaultCpmRate($slotOpportunities);

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
}