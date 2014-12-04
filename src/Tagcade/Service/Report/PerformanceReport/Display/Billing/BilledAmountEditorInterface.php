<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Model\User\Role\PublisherInterface;
use DateTime;

interface BilledAmountEditorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param $billingRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return $this
     */
    public function updateBilledAmountForPublisher(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @return int 1 if there is update, 0 otherwise
     */
    public function updateBilledAmountToCurrentDateForPublisher(PublisherInterface $publisher);

    /**
     * @return int number of updated publishers
     */
    public function updateBilledAmountToCurrentDateForAllPublishers();
}