<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use Psr\Log\LoggerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use DateTime;

interface BilledAmountEditorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param $billingRate
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return bool true if there is update false otherwise
     */
    public function updateHistoricalBilledAmount(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $date month that the update should happens. Default is current month
     * @return bool true if there is update, false otherwise
     */
    public function updateBilledAmountThresholdForPublisher(PublisherInterface $publisher, DateTime $date = null);

    /**
     * @param DateTime $date month that the update should happens. Default is yesterday
     * @return int number of updated publishers.
     */
    public function updateBilledAmountThresholdForAllPublishers(DateTime $date = null);

    public function setLogger(LoggerInterface $logger);

}