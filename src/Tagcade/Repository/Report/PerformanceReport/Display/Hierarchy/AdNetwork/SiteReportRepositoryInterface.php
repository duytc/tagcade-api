<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

interface SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate);
}