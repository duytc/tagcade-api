<?php


namespace Tagcade\Service\Report\VideoReport\Billing;


use DateTime;
use Psr\Log\LoggerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BilledAmountEditorInterface
{
    /**
     * @param DateTime|null $date
     * @return mixed
     */
    public function updateVideoBilledAmountThresholdForAllPublishers(DateTime $date = null);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime|null $date
     * @return mixed
     */
    public function updateVideoBilledAmountThresholdForPublisher(PublisherInterface $publisher, DateTime $date = null);

    /**
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(LoggerInterface $logger);
}