<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherReportRepositoryInterface
{
    /**
     * get report for a publisher
     * @param SubPublisherInterface $subPublisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param SubPublisherReportInterface $report
     * @return mixed
     */
    public function override(SubPublisherReportInterface $report);
}