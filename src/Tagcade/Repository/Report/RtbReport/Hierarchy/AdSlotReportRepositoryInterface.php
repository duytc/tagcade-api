<?php

namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\Core\ReportableAdSlotInterface;

interface AdSlotReportRepositoryInterface
{
    public function getReportFor(ReportableAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate);
}