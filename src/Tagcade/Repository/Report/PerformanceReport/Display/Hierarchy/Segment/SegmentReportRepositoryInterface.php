<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\SegmentInterface;

interface SegmentReportRepositoryInterface
{
    public function getReportFor(SegmentInterface $segment, DateTime $startDate, DateTime $endDate);
}