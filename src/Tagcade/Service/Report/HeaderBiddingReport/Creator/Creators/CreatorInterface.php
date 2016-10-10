<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SubReportInterface;

interface CreatorInterface
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
     * @param ReportTypeInterface $reportType
     * @return ReportInterface|SubReportInterface
     */
    public function createReport(ReportTypeInterface $reportType);

    /**
     * @param ReportTypeInterface $reportType
     * @return mixed
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}