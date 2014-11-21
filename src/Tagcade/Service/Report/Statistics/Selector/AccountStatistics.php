<?php

namespace Tagcade\Service\Report\Statistics\Selector;

use Tagcade\Domain\DTO\Report\Statistics\AccountsStatisticsDTO;
use Tagcade\Domain\DTO\Report\Statistics\SitesStatisticsDTO;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface as StatisticsTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;
use DateTime;

class AccountStatistics extends AbstractSelector implements AccountStatisticsInterface
{
    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;

    /**
     * @var UserManagerInterface
     */
    protected $allPublisher;

    /**
     * @var SiteStatisticsInterface
     */
    protected $siteStatistics;

    public function __construct(ReportSelectorInterface $reportSelector, UserManagerInterface $allPublisher, SiteStatisticsInterface $siteStatistics)
    {
        $this->reportSelector = $reportSelector;
        $this->allPublisher = $allPublisher;
        $this->siteStatistics = $siteStatistics;
    }

    /**
     * @inheritdoc
     */
    public function getStatisticsForAllAccounts(DateTime $startDate = null, DateTime $endDate = null)
    {
        $allPublishers = $this->allPublisher->allPublisherRoles();

        $statistics = [];
        foreach($allPublishers as $publisher)
        {
            $statistics[] = $this->getStatistics(new AccountType($publisher), $startDate, $endDate);
        }

        return $statistics;
    }

    /**
     * @inheritdoc
     */
    public function doGetReports(AccountType $accountType, DateTime $startDate = null, DateTime $endDate = null, $deepLength = 10)
    {
        $accountReports = $this->reportSelector->getReports($accountType, $startDate, $endDate, false);

        $statisticsForAllSites = $this->siteStatistics->getStatisticsForAllSites($accountType, $startDate, $endDate);

        $totalOpportunities = 0;
        $totalBillingCost = 0;

        /**
         * @var AccountReportInterface $report
         */
        foreach($accountReports as $report) {
            $totalOpportunities += $report->getTotalOpportunities();
            $totalBillingCost += $report->getBillingCost();
        }

        $topSites = [];

        if($accountReports != null)
        {
            $topSites = $this->topList($statisticsForAllSites, $deepLength);
        }

        return new AccountsStatisticsDTO($startDate, $endDate, $totalBillingCost, $totalOpportunities, $accountType->getPublisher(), $topSites);
    }

    /**
     * @param array $statisticsList
     * @param int $len
     * @return array
     */
    protected function topList(array $statisticsList, $len = 10)
    {
        /**
         * @var SitesStatisticsDTO $row
         */
        foreach ($statisticsList as $key => $row) {
            $sort[$key]  = $row->getTotalBillingCost();
        }

        array_multisort($sort, SORT_DESC, $statisticsList);

        return array_slice($statisticsList, 0, $len);
    }

    /**
     * @param StatisticsTypeInterface $statisticsType
     * @return bool
     */
    public function supportsStatisticsType(StatisticsTypeInterface $statisticsType)
    {
        return $statisticsType instanceof AccountType;
    }
}