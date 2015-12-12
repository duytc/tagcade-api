<?php

namespace Tagcade\Repository\Report\UnifiedReport;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface UnifiedReportRepositoryInterface extends ObjectRepository
{
    /**
     * get Average Values
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getAverageValues(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * get Count
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return int
     */
    public function getCount(PublisherInterface $publisher, UnifiedReportParams $params);

    /**
     * get Items
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getItems(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);
}