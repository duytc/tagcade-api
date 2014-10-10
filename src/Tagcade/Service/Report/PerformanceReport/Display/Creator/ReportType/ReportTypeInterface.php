<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use DateTime;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

interface ReportTypeInterface
{
    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param EventCounterInterface $eventCounter
     * @return self
     */
    public function setEventCounter(EventCounterInterface $eventCounter);

    /**
     * @return EventCounterInterface
     * @throws RuntimeException
     */
    public function getEventCounter();

    /**
     * @param mixed $parameter
     * @return ReportInterface
     */
    public function createReport($parameter);

    /**
     * Confirms that the parameter is valid for this report type
     *
     * @param mixed $parameter
     * @return bool
     */
    public function checkParameter($parameter);
}