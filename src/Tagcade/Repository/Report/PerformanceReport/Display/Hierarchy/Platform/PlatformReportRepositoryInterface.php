<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;

interface PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate);
}