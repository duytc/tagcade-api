<?php


namespace Tagcade\Repository\Pager\UnifiedReport\PulsePoint;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Pager\UnifiedReport\UnifiedReportRepositoryInterface;

interface AccountManagementRepositoryInterface extends UnifiedReportRepositoryInterface
{
    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}