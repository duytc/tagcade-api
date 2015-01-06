<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;

class ActionLogRepository extends EntityRepository implements ActionLogRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function getLogsForDateRange(DateTime $startDate, DateTime $endDate, $offset=0, $limit=10)
    {
        $qb = $this->createQueryBuilder('l');

        $qb = $qb
            ->where($qb->expr()->between('l.createdAt', ':startDate', ':endDate'))
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->addOrderBy('l.id', 'desc')
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
        $qb = $this->createQueryBuilder('l');

        $result = $qb
            ->select('count(l)')
            ->where($qb->expr()->between('l.createdAt', ':startDate', ':endDate'))
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate->modify('+1 day'), Type::DATE)
            ->getQuery()
            ->getSingleScalarResult();
        ;

        return $result !== null ? $result : 0;
    }

}
