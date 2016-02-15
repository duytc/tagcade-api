<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return int
     */
    public function getSumSlotOpportunities(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    public function getSumBilledAmountForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    public function getSumRevenueForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    public function getStatsSummaryForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * This will return array of pair (publisher id, billedAmount) sorted by billedAmount desc
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $limit
     * @return array
     */
    public function getTopPublishersByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10);
}