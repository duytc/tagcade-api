<?php

namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;

interface PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate);
}