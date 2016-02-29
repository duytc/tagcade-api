<?php


namespace Tagcade\Repository\Report\RtbReport;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

abstract class AbstractReportRepository extends EntityRepository
{
    /**
     * get Reports In Range Query Builder
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return \Doctrine\ORM\QueryBuilder with alias 'r'
     */
    protected function getReportsInRangeQuery(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->addOrderBy('r.opportunities', 'desc')
            ;
    }
}