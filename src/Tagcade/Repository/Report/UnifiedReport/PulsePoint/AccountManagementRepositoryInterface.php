<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;

interface AccountManagementRepositoryInterface extends UnifiedReportRepositoryInterface
{
    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}