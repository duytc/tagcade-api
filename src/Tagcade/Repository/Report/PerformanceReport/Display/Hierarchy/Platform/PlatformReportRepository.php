<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSumBilledAmountForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.billedAmount) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

}