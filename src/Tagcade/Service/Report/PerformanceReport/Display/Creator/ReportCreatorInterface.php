<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

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
     * @param ReportTypeInterface $reportType
     * @return ReportTypeInterface
     * @throws InvalidArgumentException usually if the parameter is incorrect for the supplied report type or the
     *                                  report type does not exist
     */
    public function getReport(ReportTypeInterface $reportType);
}