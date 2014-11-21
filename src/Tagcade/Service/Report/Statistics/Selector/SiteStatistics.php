<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use Tagcade\Domain\DTO\Report\Statistics\SitesStatisticsDTO;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use DateTime;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountType;

class SiteStatistics extends AbstractSelector implements SiteStatisticsInterface
{
    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    public function __construct(ReportSelectorInterface $reportSelector, SiteManagerInterface $siteManager)
    {
        $this->reportSelector = $reportSelector;
        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     */
    public function getStatisticsForAllSites(AccountType $accountType, DateTime $startDate = null, DateTime $endDate = null)
    {
        $allSites = $this->siteManager->getSitesForPublisher($accountType->getPublisher());

        $statistics = [];
        foreach($allSites as $site)
        {
            $statistics[] = $this->getStatistics(new SiteType($site), $startDate, $endDate);
        }

        return $statistics;
    }

    /**
     * @inheritdoc
     */
    public function doGetReports(SiteType $siteType, DateTime $startDate = null, DateTime $endDate = null)
    {
        $siteReports = $this->reportSelector->getReports($siteType, $startDate, $endDate, false);

        $totalOpportunities = 0;
        $totalBillingCost = 0;

        /**
         * @var SiteReportInterface $report
         */
        foreach($siteReports as $report) {
            $totalOpportunities += $report->getTotalOpportunities();
            $totalBillingCost += $report->getBillingCost();
        }

        return new SitesStatisticsDTO($startDate, $endDate, $totalBillingCost, $totalOpportunities);
    }

    /**
     * @param StatisticsTypeInterface $statisticsType
     * @return bool
     */
    public function supportsStatisticsType(StatisticsTypeInterface $statisticsType)
    {
        return $statisticsType instanceof SiteType;
    }
}
