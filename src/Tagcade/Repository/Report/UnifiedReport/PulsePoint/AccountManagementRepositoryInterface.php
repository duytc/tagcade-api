<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\UnifiedReportRepositoryInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface AccountManagementRepositoryInterface extends UnifiedReportRepositoryInterface
{
    /**
     * get Items For AdTagGroup-Day report
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return mixed
     */
    public function getItemsForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);

    /**
     * get Average Values For AdTagGroup-Day report
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getAverageValuesForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * get Count For AdTagGroup-Day
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getCountForAdTagGroupDay(PublisherInterface $publisher, UnifiedReportParams $params);
}