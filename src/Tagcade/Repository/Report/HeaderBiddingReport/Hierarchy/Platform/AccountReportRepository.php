<?php


namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\HeaderBiddingReport\AbstractReportRepository;

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

    public function getSumSlotHbRequests(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.requests) as total')
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

    public function getAggregatedReportsByDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);
        $qb ->join('r.publisher', 'p')
            ->andWhere($qb->expr()->orX('p.testAccount = 0', 'p.testAccount IS NULL'))
            ->andWhere('p.enabled = 1')
        ;
        $qb->select('
            SUM(r.requests) as requests,
            SUM(r.billedAmount) as billedAmount
            '
        );

        return current($qb->getQuery()->getArrayResult());
    }
}