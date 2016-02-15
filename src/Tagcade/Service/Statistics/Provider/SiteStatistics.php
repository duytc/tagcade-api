<?php

namespace Tagcade\Service\Statistics\Provider;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportRepositoryInterface;
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
    /**
     * @var SiteReportRepositoryInterface
     */
    private $siteReportRepository;

    public function __construct(ReportBuilderInterface $reportBuilder, ProjectedBillingCalculatorInterface $projectedBillingCalculator, SiteReportRepositoryInterface $siteReportRepository)
    {
        $this->reportBuilder = $reportBuilder;
        $this->projectedBillingCalculator = $projectedBillingCalculator;
        $this->siteReportRepository = $siteReportRepository;
    }

    public function getTopSitesForPublisherByTotalOpportunities(PublisherInterface $publisher, Params $params, $limit = 10)
    {
        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getPublisherSitesReport($publisher, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'totalOpportunities', $limit);
    }

    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, Params $params, $limit = 10)
    {

        $topSites = $this->siteReportRepository->getTopSitesForPublisherByEstRevenue($publisher, $params->getStartDate(), $params->getEndDate());
        $mySites = [];
        foreach ($topSites as $siteObj) {
            if (!array_key_exists('id', $siteObj)) {
                throw new \LogicException('Expect id in site object');
            }

            $mySites[] = $siteObj['id'];
        }

        $params->setGrouped(true);
        $allSitesByPublisherReports = $this->reportBuilder->getSitesReport($mySites, $params);

        return $this->topList($allSitesByPublisherReports, $sortBy = 'estRevenue', $limit);
    }

    public function getTopSitesByBilledAmount(Params $params, $limit = 10)
    {
        // We should not get report of all sites and trunk for top sites.
        // in stead, we get top sites first and then get report for those sites only

        // Step 1. Get top sites base on billedAmount
        $topSites = $this->siteReportRepository->getTopSitesByBilledAmount($params->getStartDate(), $params->getEndDate(), $limit);
        $mySites = [];
        foreach ($topSites as $siteObj) {
            if (!array_key_exists('id', $siteObj)) {
                throw new \LogicException('Expect id in site object');
            }

            $mySites[] = $siteObj['id'];
        }

        // Step 2. Get reports for these sites
        $params->setGrouped(true);
        $allSiteReports = $this->reportBuilder->getSitesReport($mySites, $params);

        return $this->topList($allSiteReports, $sortBy = 'billedAmount', $limit);
    }

    public function getProjectedBilledAmount(SiteInterface $site)
    {
        return $this->projectedBillingCalculator->calculateProjectedBilledAmountForSite($site);
    }

}
