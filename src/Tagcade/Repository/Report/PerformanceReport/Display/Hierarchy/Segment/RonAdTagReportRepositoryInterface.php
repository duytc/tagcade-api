<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\RonAdTagInterface;

interface RonAdTagReportRepositoryInterface
{
    public function getReportFor(RonAdTagInterface $ronAdTag, DateTime $startDate, DateTime $endDate);
}