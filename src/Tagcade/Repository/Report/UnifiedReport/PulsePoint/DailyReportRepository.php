<?php

namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\UnifiedReport\Selector\UnifiedReportParams;

class DailyReportRepository extends AbstractReportRepository implements DailyReportRepositoryInterface
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.date', 'ASC');
    }

    /**
     * @param UnifiedReportParams $params
     * @return mixed
     */
    public function getQueryForPaginator(UnifiedReportParams $params)
    {
        // TODO: Implement getQueryForPaginator() method.
    }


}