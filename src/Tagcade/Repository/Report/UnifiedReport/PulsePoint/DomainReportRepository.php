<?php


namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;


use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DomainReportRepository extends AbstractReportRepository implements DomainReportRepositoryInterface
{
    /**
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getQueryForPaginator(UnifiedReportParams $params)
    {
        // TODO: Implement getQueryForPaginator() method.
    }
}