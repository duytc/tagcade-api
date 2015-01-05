<?php

namespace Tagcade\Service\Statistics;

use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics as PlatformStatisticsDTO;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;

use Tagcade\Domain\DTO\Statistics\ProjectedBilling;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Service\Statistics\Provider\AccountStatisticsInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformTypes;
use DateTime;
use Tagcade\Service\Statistics\Provider\SiteStatisticsInterface;

class Statistics implements StatisticsInterface
{
    /**
     * @var ReportBuilderInterface
     */
    protected $reportBuilder;

    /**
     * @var AccountStatisticsInterface
     */
    protected $accountStatistics;
    /**
     * @var SiteStatisticsInterface
     */
    protected $siteStatistics;


    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    protected $numberOfPreviousDays;


    /**
     * @param ReportBuilderInterface $reportBuilder
     * @param AccountStatisticsInterface $accountStatistics
     * @param SiteStatisticsInterface $siteStatistics
     * @param DateUtilInterface $dateUtil
     * @param int $numberOfPreviousDays The number of days to include in the report range
     */
    public function __construct(
        ReportBuilderInterface $reportBuilder,
        AccountStatisticsInterface $accountStatistics,
        SiteStatisticsInterface $siteStatistics,
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
        $platformReports    = $this->reportBuilder->getPlatformReport($params);
        $platformStatistics = new PlatformStatisticsDTO($platformReports, $isTodayInRange);

        $topPublishers      = $this->accountStatistics->getTopPublishersByBilledAmount($params);
        $topSites           = $this->siteStatistics->getTopSitesByBilledAmount($params);

        $todayReport = null;
        $yesterdayReport = null;
        if ($isTodayInRange) {
            $reports            = $platformReports->getReports();
            $todayReport        = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
            $yesterdayReport    = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;
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
        $accountReports     = $this->reportBuilder->getPublisherReport($publisher, $params);
        $accountStatistics  = new AccountStatisticsDTO($accountReports, $isTodayInRange);

        $todayReport = null;
        $yesterdayReport = null;
        if ($isTodayInRange) {
            $reports = $accountReports->getReports();
            $todayReport = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
            $yesterdayReport = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;
        }

        $topSites           = $this->siteStatistics->getTopSitesForPublisherByEstRevenue($publisher, $params);
        $topAdNetworks      = $this->accountStatistics->getTopAdNetworksByEstRevenueForPublisher($publisher, $params);

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
        $params = $this->_getDashboardParams($this->dateUtil->getFirstDateInMonth(), new DateTime('yesterday'));

        /**
         * @var BilledReportGroup $platformReports
         */
        $platformReports    = $this->reportBuilder->getPlatformReport($params);

        $projectedBilledAmount = $this->accountStatistics->getAllPublishersProjectedBilledAmount();

        return new ProjectedBilling($platformReports, $projectedBilledAmount);
    }

    public function getProjectedBilledAmountForPublisher(PublisherInterface $publisher)
    {
        $params = $this->_getDashboardParams($this->dateUtil->getFirstDateInMonth(), new DateTime('yesterday'));

        /**
         * @var BilledReportGroup $publisherReports
         */
        $publisherReports    = $this->reportBuilder->getPublisherReport($publisher, $params);

        $projectedBilledAmount = $this->accountStatistics->getProjectedBilledAmount($publisher);

        return new ProjectedBilling($publisherReports, $projectedBilledAmount);
    }


    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Params
     */
    private function _getDashboardParams(DateTime $startDate = null, DateTime $endDate = null)
    {
        if (null === $endDate) {
            $endDate = new DateTime('today');
        }

        if (null === $startDate) {
            $startDate = $endDate->modify(sprintf('-%d days', $this->numberOfPreviousDays));
        }

        return (new Params($startDate, $endDate))->setGrouped(true);
    }

}