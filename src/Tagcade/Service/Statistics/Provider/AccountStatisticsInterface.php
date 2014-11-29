<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface AccountStatisticsInterface
{
    /**
     * @param Params $params
     * @param int $limit
     * @return AccountStatisticsDTO[]
     */
    public function getTopPublishersByBilledAmount(Params $params, $limit = 10);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return CalculatedReportGroup[]
     */
    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 10);
}