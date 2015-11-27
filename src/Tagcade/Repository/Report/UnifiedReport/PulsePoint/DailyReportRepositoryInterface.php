<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;

interface DailyReportRepositoryInterface extends UnifiedReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}