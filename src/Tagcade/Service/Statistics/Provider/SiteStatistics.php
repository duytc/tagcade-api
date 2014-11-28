<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Fields\TopListFilter;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

class SiteStatistics implements SiteStatisticsInterface
{
    use TopListFilter;

    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;

    public function __construct(ReportBuilderInterface $reportBuilder)
    {
        $this->reportBuilder = $reportBuilder;
    }
    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC')
    {
        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getPublisherSitesReport($publisher, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'totalOpportunities', $limit, $order);
    }

    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 7, $order = 'DESC')
    {
        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getPublisherSitesReport($publisher, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'estRevenue', $limit, $order);
    }

    public function getTopSiteByBilledAmount(Params $params, $limit = 7, $order = 'DESC')
    {
        $params->setGrouped(true);
        $allSiteReports = $this->reportBuilder->getAllSitesReport($params);

        return $this->topList($allSiteReports, $sortBy = 'billedAmount', $limit, $order);
    }
}
