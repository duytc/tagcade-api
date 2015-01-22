<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\SiteInterface;

interface SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    public function getSumBilledAmountForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate);

    public function getSumSlotOpportunities(SiteInterface $site, DateTime $startDate, DateTime $endDate);
}