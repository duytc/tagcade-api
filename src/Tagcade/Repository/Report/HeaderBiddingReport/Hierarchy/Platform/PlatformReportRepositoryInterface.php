<?php

namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReportInterface;

interface PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate);

    public function overrideReport(PlatformReportInterface $report);
}