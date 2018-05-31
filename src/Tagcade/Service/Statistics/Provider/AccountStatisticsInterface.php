<?php

namespace Tagcade\Service\Statistics\Provider;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;

interface AccountStatisticsInterface
{
    /**
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopPublishersByBilledAmount(Params $params, $limit = 10);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 10);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopAdNetworksByTotalOpportunitiesForPublisher(PublisherInterface $publisher, Params $params, $limit = 10);
    /**
     * @param PublisherInterface $publisher
     * @return float
     */
    public function getProjectedBilledAmount(PublisherInterface $publisher);

    /**
     * return projected billed amount of all publishers
     * @return float
     */
    public function getAllPublishersProjectedBilledAmount();

    public function getAccountSummaryByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null);
}