<?php

namespace Tagcade\Service\Statistics;

use DateInterval;
use DatePeriod;
use DateTime;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics as PlatformStatisticsDTO;
use Tagcade\Domain\DTO\Statistics\ProjectedBilling;
use Tagcade\Domain\DTO\Statistics\Summary\PlatformSummary;
use Tagcade\Domain\DTO\Statistics\Summary\Summary;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Service\Statistics\Provider\AccountStatisticsInterface;
use Tagcade\Service\Statistics\Provider\SiteStatisticsInterface;

class Statistics implements StatisticsInterface
{
    /** @var ReportBuilderInterface */
    protected $reportBuilder;

    /** @var AccountStatisticsInterface */
    protected $accountStatistics;

    /** @var SiteStatisticsInterface */
    protected $siteStatistics;

    /** @var PlatformReportRepositoryInterface */
    protected $platformReportRepository;

    /** @var DateUtilInterface */
    protected $dateUtil;

    protected $numberOfPreviousDays;

    /**
     * @param ReportBuilderInterface $reportBuilder
     * @param AccountStatisticsInterface $accountStatistics
     * @param SiteStatisticsInterface $siteStatistics
     * @param PlatformReportRepositoryInterface $platformReportRepository
     * @param DateUtilInterface $dateUtil
     * @param int $numberOfPreviousDays The number of days to include in the report range
     */
    public function __construct(
        ReportBuilderInterface $reportBuilder,
        AccountStatisticsInterface $accountStatistics,
        SiteStatisticsInterface $siteStatistics,
        PlatformReportRepositoryInterface $platformReportRepository,
        DateUtilInterface $dateUtil,
        $numberOfPreviousDays
    )
    {
        if (!is_int($numberOfPreviousDays) || $numberOfPreviousDays < 0) {
            throw new InvalidArgumentException('Invalid number of previous days. It should be an integer and not negative');
        }

        $this->reportBuilder = $reportBuilder;
        $this->accountStatistics = $accountStatistics;
        $this->siteStatistics = $siteStatistics;
        $this->platformReportRepository = $platformReportRepository;
        $this->dateUtil = $dateUtil;

        $this->numberOfPreviousDays = $numberOfPreviousDays;

    }

    /**
     * @inheritdoc
     */
    public function getAdminDashboard(DateTime $startDate = null, DateTime $endDate = null)
    {
        $params = $this->_getDashboardParams($startDate, $endDate);
        $isTodayInRange = $this->dateUtil->isTodayInRange($params->getStartDate(), $params->getEndDate());
        /**
         * @var BilledReportGroup $platformReports
         */
        $platformReports = $this->reportBuilder->getPlatformReport($params);
        if (false === $platformReports) {
            return new AdminDashboard();
        }

        $platformStatistics = new PlatformStatisticsDTO($platformReports);

        $topPublishers = $this->accountStatistics->getTopPublishersByBilledAmount($params);
        $topSites = $this->siteStatistics->getTopSitesByBilledAmount($params);

        $todayReport = null;
        $yesterdayReport = null;
        if ($isTodayInRange) {
            $reports = $platformReports->getReports();
            $todayReport = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
            $yesterdayReport = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;
        }

        return new AdminDashboard(
            $platformStatistics,
            new DaySummary($todayReport),
            new DaySummary($yesterdayReport),
            $topPublishers,
            $topSites
        );
    }

    /**
     * @inheritdoc
     */
    public function getPublisherDashboard(PublisherInterface $publisher, DateTime $startDate = null, DateTime $endDate = null)
    {
        $params = $this->_getDashboardParams($startDate, $endDate);
        $isTodayInRange = $this->dateUtil->isTodayInRange($params->getStartDate(), $params->getEndDate());

        /**
         * @var BilledReportGroup $accountReports
         */
        $accountReports = $this->reportBuilder->getPublisherReport($publisher, $params);
        if (false === $accountReports) {
            return new PublisherDashboard();
        }
        $accountStatistics = new AccountStatisticsDTO($accountReports);

        $todayReport = null;
        $yesterdayReport = null;
        if ($isTodayInRange) {
            $reports = $accountReports->getReports();
            $todayReport = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
            $yesterdayReport = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;
        }

        $topSites = $this->siteStatistics->getTopSitesForPublisherByEstRevenue($publisher, $params);
        $topAdNetworks = $this->accountStatistics->getTopAdNetworksByEstRevenueForPublisher($publisher, $params);

        return new PublisherDashboard(
            $accountStatistics,
            new DaySummary($todayReport),
            new DaySummary($yesterdayReport),
            $topSites,
            $topAdNetworks
        );
    }

    public function getProjectedBilledAmountForAllPublishers()
    {
        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth();
        $yesterday = new DateTime('yesterday');
        if ($yesterday < $firstDateInMonth) {
            return new ProjectedBilling(); // no projected bill on first day of month
        }

        $params = $this->_getDashboardParams($firstDateInMonth, $yesterday);

        /**
         * @var BilledReportGroup $platformReports
         */
        $platformReports = $this->reportBuilder->getPlatformReport($params);

        $projectedBilledAmount = $this->accountStatistics->getAllPublishersProjectedBilledAmount();

        return new ProjectedBilling($platformReports === false ? null : $platformReports, $projectedBilledAmount);
    }

    public function getProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth();
        $yesterday = new DateTime('yesterday');
        if ($yesterday < $firstDateInMonth) {
            return new ProjectedBilling(); // no projected bill on first day of month
        }

        $params = $this->_getDashboardParams($firstDateInMonth, $yesterday);

        /**
         * @var BilledReportGroup $publisherReports
         */
        $publisherReports = $this->reportBuilder->getPublisherReport($publisher, $params);
        if (false === $publisherReports) {
            return new ProjectedBilling();
        }

        $projectedBilledAmount = $this->accountStatistics->getProjectedBilledAmount($publisher);

        return new ProjectedBilling($publisherReports, $projectedBilledAmount);
    }

    public function getProjectedBilledAmountForSite(SiteInterface $site)
    {
        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth();
        $yesterday = new DateTime('yesterday');
        if ($yesterday < $firstDateInMonth) {
            return new ProjectedBilling(); // no projected bill on first day of month
        }

        $params = $this->_getDashboardParams($firstDateInMonth, $yesterday);
        /**
         * @var BilledReportGroup $siteReports
         */
        $siteReports = $this->reportBuilder->getSiteReport($site, $params);
        if (false === $siteReports) {
            return new ProjectedBilling();
        }

        $projectedBilledAmount = $this->siteStatistics->getProjectedBilledAmount($site);

        return new ProjectedBilling($siteReports, $projectedBilledAmount);
    }

    public function getAccountSummaryByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null)
    {
        return $this->accountStatistics->getAccountSummaryByMonth($publisher, $startMonth, $endMonth);
    }

    public function getPlatformSummaryByMonth(DateTime $startMonth, DateTime $endMonth = null)
    {
        if (null === $endMonth) {
            $endMonth = new DateTime('today');
            $endMonth = $endMonth->modify('-1 month');
        }

        if ($startMonth > $endMonth) {
            throw new InvalidArgumentException('Start month must not exceed end month');
        }

        $interval = new DateInterval('P1M');
        $monthRange = new DatePeriod($startMonth, $interval, $endMonth);

        $summaries = [];
        foreach ($monthRange as $month) {
            $summaries[] = $this->getPlatformSummaryForMonth($month);
        }

        return $summaries;
    }

    protected function getPlatformSummaryForMonth(DateTime $month)
    {
        if (null === $month) {
            $month = new DateTime('today');
            $month = $month->modify('-1 month');
        }

        $month = $this->dateUtil->getFirstDateInMonth($month);
        $thisMonth = $this->dateUtil->getFirstDateInMonth(new DateTime('today'));

        if ($month >= $thisMonth) {
            throw new InvalidArgumentException('Expect last month or further in the past');
        }

        $summary = $this->platformReportRepository->getStatsSummaryForDateRange($month, $this->dateUtil->getLastDateInMonth($month));
        return new PlatformSummary(
            $month,
            new Summary((int)$summary['slotOpportunities'], (int)$summary['totalOpportunities'], (int)$summary['impressions'], (float)$summary['totalBilledAmount'], (float)$summary['totalEstRevenue'])
        );
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Params
     */
    private function _getDashboardParams(DateTime $startDate = null, DateTime $endDate = null)
    {
        if (null === $endDate && null === $startDate) {
            $endDate = new DateTime('yesterday');
            // if we just modify endDate, both date objects point to the same object
            $startDate = (new DateTime('yesterday'))->modify(sprintf('-%d days', $this->numberOfPreviousDays));
        } elseif (null === $endDate) {
            // start date is set, so set end date to be the same date
            // this is very important, we want separate date objects, otherwise the call to setTime below will affect both dates
            $endDate = clone $startDate;
        }

        $endDate->setTime(23, 59, 59);

        return (new Params($startDate, $endDate))->setGrouped(true);
    }
}