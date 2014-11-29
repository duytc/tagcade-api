<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface SiteStatisticsInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return CalculatedReportGroup[]
     */
    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 10);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return CalculatedReportGroup[]
     */
    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 10);

    /**
     * @param Params $params
     * @param int $limit
     * @return CalculatedReportGroup[]
     */
    public function getTopSitesByBilledAmount(Params $params, $limit = 10);
} 