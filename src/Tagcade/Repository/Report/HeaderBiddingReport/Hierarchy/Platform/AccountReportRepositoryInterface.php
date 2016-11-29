<?php

namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AccountReportRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return AccountReportInterface
     */
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getSumSlotHbRequests(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate);

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getAggregatedReportsByDateRange(DateTime $startDate, DateTime $endDate);
}