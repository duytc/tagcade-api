<?php

namespace Tagcade\Repository\Report\UnifiedReport;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface UnifiedReportRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @param int $defaultPageSize
     * @return array
     */
    public function getReports(PublisherInterface $publisher, UnifiedReportParams $params, $defaultPageSize = 10);
}