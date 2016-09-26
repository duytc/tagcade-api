<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

abstract class AbstractVideoReportRepository extends EntityRepository
{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);

        return $qb
            ->orderBy('r.date', 'desc');
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    public function getReportsByDateRange(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getReportsByDateRangeQuery(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE);
    }
} 