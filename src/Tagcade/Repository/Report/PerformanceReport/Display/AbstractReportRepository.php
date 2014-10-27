<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use DateTime;

abstract class AbstractReportRepository extends EntityRepository
{
    protected function getReportsInRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->orderBy('r.date', 'desc')
        ;
    }
}