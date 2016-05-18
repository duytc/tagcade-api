<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountReportRepository extends AbstractReportRepository implements AccountReportRepositoryInterface
{
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAggregatedReportsByDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);
        $qb ->join('r.publisher', 'p')
            ->andWhere($qb->expr()->orX('p.testAccount = 0', 'p.testAccount IS NULL'))
            ->andWhere('p.enabled = 1')
        ;
        $qb->select('
            SUM(r.totalOpportunities) as totalOpportunities,
            SUM(r.slotOpportunities) as slotOpportunities,
            SUM(r.impressions) as impressions,
            SUM(r.rtbImpressions) as rtbImpressions,
            SUM(r.passbacks) as passbacks,
            SUM(r.billedAmount) as billedAmount
            '
        );

        return current($qb->getQuery()->getArrayResult());
    }

    public function getSumSlotOpportunities(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.slotOpportunities) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getSumBilledAmountForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.billedAmount) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getSumRevenueForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.estRevenue) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getStatsSummaryForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.totalOpportunities) as totalOpportunities, SUM(r.slotOpportunities) as slotOpportunities, SUM(r.impressions) as impressions, SUM(r.estRevenue) as totalEstRevenue, SUM(r.billedAmount) as totalBilledAmount')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    public function getTopPublishersByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('pr');
        $qb->select('p.id, SUM(pr.billedAmount) AS totalBilledAmount')
            ->join('pr.publisher', 'p')
            ->where($qb->expr()->between('pr.date', ':startDate', ':endDate'))
            ->andWhere('p.id = pr.publisher')
            ->andWhere('p.enabled = 1')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->groupBy('pr.publisher')
            ->orderBy('totalBilledAmount', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }


}