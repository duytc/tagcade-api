<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

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
     * @param int $slotOpportunities
     * @return float
     */
    public function getBilledRateForPublisher(PublisherInterface $publisher, $slotOpportunities);

    /**
     * @param PublisherInterface $publisher
     * @return float
     */
    public function getLastRateForPublisher(PublisherInterface $publisher);
}