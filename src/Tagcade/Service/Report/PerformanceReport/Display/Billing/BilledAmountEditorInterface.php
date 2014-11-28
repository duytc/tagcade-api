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
}