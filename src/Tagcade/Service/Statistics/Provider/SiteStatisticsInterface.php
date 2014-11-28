<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Statistics\ProviderStatisticsInterface;

interface SiteStatisticsInterface extends ProviderStatisticsInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @param string $order
     * @return CalculatedReportGroup[]
     */
    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC');

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @param string $order
     * @return CalculatedReportGroup[]
     */
    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC');

    /**
     * @param Params $params
     * @param int $limit
     * @param string $order
     * @return CalculatedReportGroup[]
     */
    public function getTopSiteByBilledAmount(Params $params, $limit = 7, $order = 'DESC');
} 