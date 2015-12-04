<?php


namespace Tagcade\Repository\Pager\UnifiedReport\PulsePoint;


use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Pager\UnifiedReport\UnifiedReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface AccountManagementRepositoryInterface extends UnifiedReportRepositoryInterface
{
    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, UnifiedReportParams $params);
}