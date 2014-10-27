<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;

interface AdNetworkReportRepositoryInterface
{
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate);
}