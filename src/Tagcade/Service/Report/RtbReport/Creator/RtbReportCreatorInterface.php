<?php

namespace Tagcade\Service\Report\RtbReport\Creator;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;

interface RtbReportCreatorInterface
{
    /**
     * @param \DateTime $date
     * @return self
     */
    public function setDate(\DateTime $date);

    /**
     * @return \DateTime|null
     */
    public function getDate();

    /**
     * @return RtbEventCounterInterface
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