<?php

namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\BaseAdSlotInterface;

interface AdSlotReportRepositoryInterface
{
    public function getReportFor(BaseAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate);
}