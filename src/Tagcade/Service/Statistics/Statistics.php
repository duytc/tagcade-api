<?php

namespace Tagcade\Service\Statistics;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\Hierarchy\Platform\CalculatedReportGroup;
use Tagcade\Domain\DTO\Statistics\DaySummary;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\Overview;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\PlatformStatistics as PlatformStatisticsDTO;
use Tagcade\Domain\DTO\Statistics\Hierarchy\Platform\AccountStatistics as AccountStatisticsDTO;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
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

    protected $numberOfPreviousDays;

    /**
     * @param ReportBuilderInterface $reportBuilder
     * @param AccountStatisticsInterface $accountStatistics
     * @param SiteStatisticsInterface $siteStatistics
     * @param int $numberOfPreviousDays The number of days to include in the report range
     */
    public function __construct(ReportBuilderInterface $reportBuilder, AccountStatisticsInterface $accountStatistics, SiteStatisticsInterface $siteStatistics, $numberOfPreviousDays = 7)
    {
        if (!is_int($numberOfPreviousDays) || $numberOfPreviousDays < 0) {
            throw new InvalidArgumentException('Invalid number of previous days. It should be an integer and not negative');
        }

        $this->reportBuilder = $reportBuilder;
        $this->accountStatistics = $accountStatistics;
        $this->siteStatistics = $siteStatistics;

        $this->numberOfPreviousDays = $numberOfPreviousDays;
    }

    /**
     * @inheritdoc
     */
    public function getAdminDashboard()
    {
        $params = $this->_getDashboardParams();

        $platformReports    = $this->reportBuilder->getPlatformReport($params);
        $platformStatistics = new PlatformStatisticsDTO($platformReports);

        $topPublishers      = $this->accountStatistics->getTopPublishersByBilledAmount($params);
        $topSites           = $this->siteStatistics->getTopSitesByBilledAmount($params);

        $reports            = $platformReports->getReports();
        $todayReport        = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
        $yesterdayReport    = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;

        return new AdminDashboard(
            $platformStatistics,
            new DaySummary($todayReport),
            new DaySummary($yesterdayReport),
            $topPublishers,
            $topSites,
            $reports
        );
    }

    /**
     * @inheritdoc
     */
    public function getPublisherDashboard(PublisherInterface $publisher)
    {
        $params = $this->_getDashboardParams();

        /**
         * @var CalculatedReportGroup $accountReports
         */
        $accountReports     = $this->reportBuilder->getPublisherReport($publisher, $params);
        $accountStatistics  = new AccountStatisticsDTO($accountReports);

        $reports            = $accountReports->getReports();
        $todayReport        = count($reports) > 0 ? array_slice($reports, 0, 1)[0] : null;
        $yesterdayReport    = count($reports) > 1 ? array_slice($reports, 1, 1)[0] : null;

        $topSites           = $this->siteStatistics->getTopSitesForPublisherByEstRevenue($publisher, $params);
        $topAdNetworks      = $this->accountStatistics->getTopAdNetworksByEstRevenueForPublisher($publisher, $params);

        return new PublisherDashboard(
            $accountStatistics,
            new DaySummary($todayReport),
            new DaySummary($yesterdayReport),
            $topSites,
            $topAdNetworks,
            $reports
        );
    }

    /**
     * @return Params
     */
    private function _getDashboardParams()
    {
        $startDate = new DateTime($this->numberOfPreviousDays . ' days ago');
        $endDate = new DateTime('today');

        return (new Params($startDate, $endDate))->setGrouped(true);
    }

}