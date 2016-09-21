<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators;

use DateTime;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface;

interface CreatorInterface
{
    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param VideoEventCounterInterface $eventCounter
     * @return self
     */
    public function setEventCounter(VideoEventCounterInterface $eventCounter);

    /**
     * @return VideoEventCounterInterface
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