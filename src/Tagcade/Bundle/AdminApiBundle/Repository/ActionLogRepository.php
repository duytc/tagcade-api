<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\User\Role\PublisherInterface;

class ActionLogRepository extends EntityRepository implements ActionLogRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function getLogsForDateRange(DateTime $startDate, DateTime $endDate, $offset = 0, $limit = 10, PublisherInterface $publisher = null, $loginLog = false)
    {
        $qb = $this->createLogsQueryBuilder($startDate, $endDate, $publisher, $loginLog);

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
    public function getTotalRows(DateTime $startDate, DateTime $endDate, PublisherInterface $publisher = null, $loginLog = false)
    {
        $qb = $this->createLogsQueryBuilder($startDate, $endDate, $publisher, $loginLog);

        $result = $qb->select('count(l)')->getQuery()->getSingleScalarResult();

        return $result !== null ? $result : 0;
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param PublisherInterface $publisher
     * @param bool $loginLog
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createLogsQueryBuilder(DateTime $startDate, DateTime $endDate, PublisherInterface $publisher = null, $loginLog = false)
    {
        $qb = $this->createQueryBuilder('l');

        $qb = $qb
            ->where($qb->expr()->between('l.createdAt', ':startDate', ':endDate'))
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->addOrderBy('l.id', 'desc')
        ;

        if (null !== $publisher) {
            $qb ->andWhere('l.user = :user')
                ->setParameter('user', $publisher);
        }

        $qb ->andWhere($loginLog ? 'l.action = :action' : 'l.action <> :action')
            ->setParameter('action', 'LOGIN');

        return $qb;
    }

}
