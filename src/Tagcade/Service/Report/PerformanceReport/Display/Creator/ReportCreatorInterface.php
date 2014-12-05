<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;

interface ReportCreatorInterface
{
    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date);

    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @return EventCounterInterface
     */
    public function getEventCounter();

    /**
     * @param CreatorInterface $creator
     */
    public function addCreator(CreatorInterface $creator);

    /**
     * @param ReportTypeInterface $reportType
     * @return ReportTypeInterface
     * @throws InvalidArgumentException usually if the parameter is incorrect for the supplied report type or the
     *                                  report type does not exist
     */
    public function getReport(ReportTypeInterface $reportType);
}