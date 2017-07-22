<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotReportRepositoryInterface
{
    /**
     * @param ReportableAdSlotInterface $adSlot
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getReportFor(ReportableAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getAllReportInRange(DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getAllReportInRangeForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param AdSlotReportInterface $report
     * @return mixed
     */
    public function overrideReport(AdSlotReportInterface $report);
}