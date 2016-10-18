<?php

namespace Tagcade\Repository\Report\HeaderBiddingReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use DateTime;

abstract class AbstractReportRepository extends EntityRepository
{
    protected function getReportsInRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);

        return $qb
            ->orderBy('r.date', 'desc')
            ->addOrderBy('r.requests', 'desc')
        ;
    }

    public function getReportsByDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);

        return $qb->getQuery()->getResult();
    }

    protected function getReportsByDateRangeQuery(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
         ;
    }
}