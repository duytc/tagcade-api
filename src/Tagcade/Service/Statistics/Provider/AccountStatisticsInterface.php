<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Statistics\ProviderStatisticsInterface;

interface AccountStatisticsInterface extends ProviderStatisticsInterface
{
    /**
     * @param Params $params
     * @param int $limit
     * @param string $order
     * @return AccountStatisticsDTO[]
     */
    public function getTopPublishersByBilledAmount(Params $params, $limit = 7, $order = 'DESC');

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @param string $order
     * @return CalculatedReportGroup[]
     */
    public function getTopAdNetworksByEstRevenueForPublisher(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC');
}