<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

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
     * @return float
     */
    public function getProjectedBilledAmount(PublisherInterface $publisher);

    /**
     * return projected billed amount of all publishers
     * @return float
     */
    public function getAllPublishersProjectedBilledAmount();

}