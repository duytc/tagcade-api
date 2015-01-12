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

    public function getStatsSummaryForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.totalOpportunities) as totalOpportunities, SUM(r.slotOpportunities) as slotOpportunities, SUM(r.impressions) as impressions,SUM(r.estRevenue) as totalEstRevenue, SUM(r.billedAmount) as totalBilledAmount')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }


}