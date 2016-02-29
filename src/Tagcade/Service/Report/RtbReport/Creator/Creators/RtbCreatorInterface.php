<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\RtbReport\SubReportInterface;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;

interface RtbCreatorInterface
{
    /**
     * @return \DateTime|null
     */
    public function getDate();

    /**
     * @param RtbEventCounterInterface $eventCounter
     * @return self
     */
    public function setEventCounter(RtbEventCounterInterface $eventCounter);

    /**
     * @return RtbEventCounterInterface
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