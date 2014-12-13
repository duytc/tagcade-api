<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

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
     * @param DateTime $date
     * @return float threshold rate of current publisher on a specific month
     */
    public function getThresholdRateForPublisher(PublisherInterface $publisher, DateTime $date = null);

    /**
     * @param PublisherInterface $publisher
     * @return CpmRate
     */
    public function getTodayCpmRateForPublisher(PublisherInterface $publisher);

}