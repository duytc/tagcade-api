<?php


namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Repository\Report\RtbReport\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->getQuery()
            ->getResult()
        ;
    }
}