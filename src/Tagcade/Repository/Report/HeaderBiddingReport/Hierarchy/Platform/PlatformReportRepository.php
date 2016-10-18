<?php


namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Repository\Report\HeaderBiddingReport\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->getQuery()
            ->getResult()
        ;
    }
}