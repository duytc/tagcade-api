<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;

class ActionLogRepository extends EntityRepository implements ActionLogRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function getLogsForDateRange(DateTime $startDate, DateTime $endDate, $offset=0, $limit=10)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
        ;

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getTotalRows(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('count(l)')
            ->where('l.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
        ;

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result !== null ? $result : 0;
    }

} 