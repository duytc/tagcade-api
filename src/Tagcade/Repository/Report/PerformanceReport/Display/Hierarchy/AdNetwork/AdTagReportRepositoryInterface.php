<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;

interface AdTagReportRepositoryInterface
{
    public function getReportFor(AdTagInterface $adTag, DateTime $startDate, DateTime $endDate);
}