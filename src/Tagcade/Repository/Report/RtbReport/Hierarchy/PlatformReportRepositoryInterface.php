<?php

namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;

interface PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate);
}