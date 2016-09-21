<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators;

use Psr\Log\LoggerInterface;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;
use Tagcade\Exception\RuntimeException;

abstract class CreatorAbstract implements CreatorInterface
{
    /**
     * @var VideoEventCounterInterface|null
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
    public function setEventCounter(VideoEventCounterInterface $eventCounter)
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
        return $this->eventCounter instanceof VideoEventCounterInterface;
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