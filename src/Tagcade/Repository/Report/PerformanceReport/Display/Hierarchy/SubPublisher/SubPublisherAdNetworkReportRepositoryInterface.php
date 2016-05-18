<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherAdNetworkReportRepositoryInterface
{
    /**
     * get report for a publisher
     * @param SubPublisherInterface $subPublisher
     * @param AdNetworkInterface $adNetwork
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return mixed
     */
    public function getReportFor(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false);


    public function override(SubPublisherAdNetworkReportInterface $report);
}