<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;

interface AdTagReportRepositoryInterface
{
    public function getReportFor(AdtagInterface $adTag, DateTime $startDate, DateTime $endDate);
}