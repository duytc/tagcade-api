<?php

namespace Tagcade\Repository\Pager\UnifiedReport;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tagcade\Model\User\Role\PublisherInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

interface UnifiedReportRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param UnifiedReportParams $params
     * @return SlidingPagination
     */
    public function getReportFor(PublisherInterface $publisher, UnifiedReportParams $params);
}