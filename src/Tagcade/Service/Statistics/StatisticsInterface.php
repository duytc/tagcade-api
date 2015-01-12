<?php

namespace Tagcade\Service\Statistics;

use DateTime;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\ProjectedBilling;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformTypes;
use Tagcade\Model\User\Role\PublisherInterface;

interface StatisticsInterface
{

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return AdminDashboard
     */
    public function getAdminDashboard(DateTime $startDate = null, DateTime $endDate = null);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return PublisherDashboard
     */
    public function getPublisherDashboard(PublisherInterface $publisher, DateTime $startDate = null, DateTime $endDate = null);

    /**
     * @param PublisherInterface $publisher
     * @return ProjectedBilling
     */
    public function getProjectedBilledAmountForPublisher(PublisherInterface $publisher);

    /**
     * @return ProjectedBilling
     */
    public function getProjectedBilledAmountForAllPublishers();

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startMonth
     * @param DateTime $endMonth
     * @return array
     */
    public function getAccountBilledAmountByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null);

    /**
     * @param DateTime $startMonth
     * @param DateTime $endMonth
     * @return array
     */
    public function getPlatformBilledAmountByMonth(DateTime $startMonth, DateTime $endMonth = null);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startMonth
     * @param DateTime $endMonth
     * @return array
     */
    public function getAccountRevenueByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null);

    public function getAccountSummaryByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null);

    public function getPlatformSummaryByMonth(DateTime $startMonth, DateTime $endMonth = null);
}
