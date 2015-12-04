<?php


namespace Tagcade\Repository\Pager\UnifiedReport;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

abstract class AbstractReportRepository extends EntityRepository
{
    protected function getReportsInRange(\DateTime $startDate, \DateTime $endDate, $searchField = null, $searchKey = null)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
        ;
    }
}