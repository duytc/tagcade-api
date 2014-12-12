<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

interface CpmRateGetterInterface
{
    /**
     * @param int $slotOpportunities
     * @return float
     */
    public function getDefaultCpmRate($slotOpportunities);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $date month that we want to get rate of this publisher
     * @return float
     */
    public function getBilledRateForPublisher(PublisherInterface $publisher, DateTime $date = null);

    /**
     * @param PublisherInterface $publisher
     * @return float
     */
    public function getLastRateForPublisher(PublisherInterface $publisher);
}