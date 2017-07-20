<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkReportRepositoryInterface
{
    /**
     * @param AdNetworkInterface $adNetwork
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return array
     */
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param PublisherInterface $publisher
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool $oneOrNull
     * @return array
     */
    public function getReportForAllAdNetworkOfPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    /**
     * @param AdNetworkReportInterface $report
     * @return mixed
     */
    public function overrideReport(AdNetworkReportInterface $report);
}