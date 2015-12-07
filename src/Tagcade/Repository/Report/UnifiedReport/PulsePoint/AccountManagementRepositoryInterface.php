<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface AccountManagementRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getAdTagGroupDailyReportFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    /**
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getQueryForPaginator(UnifiedReportParams $params);
}