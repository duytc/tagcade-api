<?php

namespace Tagcade\Repository\Report\UnifiedReport\PulsePoint;

use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class DailyReportRepository extends AbstractReportRepository implements DailyReportRepositoryInterface
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = parent::getReportsInRange($startDate, $endDate);

        return $qb
            ->addOrderBy('r.date', 'ASC');
    }
}