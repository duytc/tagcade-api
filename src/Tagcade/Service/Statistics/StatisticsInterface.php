<?php

namespace Tagcade\Service\Statistics;

use Tagcade\Domain\DTO\Statistics\Dashboard\AdminDashboard;
use Tagcade\Domain\DTO\Statistics\Dashboard\PublisherDashboard;
use Tagcade\Domain\DTO\Statistics\ProjectedBilling;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformTypes;
use Tagcade\Model\User\Role\PublisherInterface;

interface StatisticsInterface
{

    /**
     * @return AdminDashboard
     */
    public function getAdminDashboard();

    /**
     * @param PublisherInterface $publisher
     * @return PublisherDashboard
     */
    public function getPublisherDashboard(PublisherInterface $publisher);

    /**
     * @param PublisherInterface $publisher
     * @return ProjectedBilling
     */
    public function getProjectedBilledAmountForPublisher(PublisherInterface $publisher);

    /**
     * @return ProjectedBilling
     */
    public function getProjectedBillingForAllPublishers();


}
