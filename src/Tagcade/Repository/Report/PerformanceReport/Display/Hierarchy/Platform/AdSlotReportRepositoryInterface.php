<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\AdSlotInterface;

interface AdSlotReportRepositoryInterface
{
    public function getReportFor(AdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate);
}