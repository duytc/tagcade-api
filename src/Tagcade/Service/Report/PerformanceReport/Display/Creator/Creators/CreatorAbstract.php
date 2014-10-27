<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Exception\RuntimeException;

abstract class CreatorAbstract implements CreatorInterface
{
    /**
     * @var EventCounterInterface|null
     */
    protected $eventCounter;

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
}