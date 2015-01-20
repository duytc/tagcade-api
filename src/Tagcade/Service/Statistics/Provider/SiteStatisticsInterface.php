<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

interface SiteStatisticsInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 10);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 10);

    /**
     * @param Params $params
     * @param int $limit
     * @return BilledReportGroup[]
     */
    public function getTopSitesByBilledAmount(Params $params, $limit = 10);

    /**
     * @param SiteInterface $site
     * @return float
     */
    public function getProjectedBilledAmount(SiteInterface $site);

} 