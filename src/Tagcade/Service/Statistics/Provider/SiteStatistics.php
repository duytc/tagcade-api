<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\ProjectedBillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Statistics\Provider\Behaviors\TopListFilterTrait;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform;

class SiteStatistics implements SiteStatisticsInterface
{
    use TopListFilterTrait;

    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;
    /**
     * @var ProjectedBillingCalculatorInterface
     */
    protected $projectedBillingCalculator;

    public function __construct(ReportBuilderInterface $reportBuilder, ProjectedBillingCalculatorInterface $projectedBillingCalculator)
    {
        $this->reportBuilder = $reportBuilder;
        $this->projectedBillingCalculator = $projectedBillingCalculator;
    }
    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getPublisherSitesReport($publisher, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'totalOpportunities', $limit);
    }

    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getPublisherSitesReport($publisher, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'estRevenue', $limit);
    }

    public function getTopSitesByBilledAmount(Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $allSiteReports = $this->reportBuilder->getAllSitesReport($params);

        return $this->topList($allSiteReports, $sortBy = 'billedAmount', $limit);
    }

    public function getProjectedBilledAmount(SiteInterface $site)
    {
        return $this->projectedBillingCalculator->calculateProjectedBilledAmountForSite($site);
    }

}
