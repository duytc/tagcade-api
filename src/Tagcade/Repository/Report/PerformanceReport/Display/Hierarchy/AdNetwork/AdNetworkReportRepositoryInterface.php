<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkReportRepositoryInterface
{
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    public function getReportForAllAdNetworkOfPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false);

    public function getPublisherAllPartnersByDay($publisherId,  DateTime $startDate, DateTime $endDate);

    public function overrideReport(AdNetworkReportInterface $report);
}