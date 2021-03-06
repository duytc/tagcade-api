<?php

namespace Tagcade\Service\Statistics;

use DateTime;
use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\ProjectedBilling;
use Tagcade\Model\Core\SiteInterface;
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


    public function getAccountSummaryByMonth(PublisherInterface $publisher, DateTime $startMonth, DateTime $endMonth = null);

    public function getPlatformSummaryByMonth(DateTime $startMonth, DateTime $endMonth = null);

    public function getProjectedBilledAmountForSite(SiteInterface $site);

}
