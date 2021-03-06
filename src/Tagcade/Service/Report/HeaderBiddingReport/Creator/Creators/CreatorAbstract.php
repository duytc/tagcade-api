<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators;

use Psr\Log\LoggerInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Exception\RuntimeException;

abstract class CreatorAbstract implements CreatorInterface
{
    /**
     * @var EventCounterInterface|null
     */
    protected $eventCounter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->getEventCounter()->getDate();
    }

    /**
     * @inheritdoc
     */
    public function setEventCounter(EventCounterInterface $eventCounter)
    {
        $this->eventCounter = $eventCounter;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEventCounter()
    {
        if (!$this->hasEventCounter()) {
            throw new RuntimeException('eventCounter was not set');
        }

        return $this->eventCounter;
    }

    protected function hasEventCounter()
    {
        return $this->eventCounter instanceof EventCounterInterface;
    }

    /**
     * @inheritdoc
     */
    public function createReport(ReportTypeInterface $reportType)
    {
        return $this->doCreateReport($reportType);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}