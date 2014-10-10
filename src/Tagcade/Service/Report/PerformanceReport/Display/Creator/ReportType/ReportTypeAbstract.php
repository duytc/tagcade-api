<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Exception\RuntimeException;

abstract class ReportTypeAbstract implements ReportTypeInterface
{
    const REPORT_TYPE = '';

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
    public function createReport($parameter)
    {
        if (!$this->checkParameter($parameter)) {
            throw new InvalidArgumentException('The supplied parameter is not valid for this report type');
        }

        return $this->doCreateReport($parameter);
    }
}