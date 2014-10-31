<?php

namespace Tagcade\Repository\Report\SourceReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ReportRepository extends EntityRepository implements ReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReports(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $qb
            ->select('r')
            ->Where('r.site = :site')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('site', $site)
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->orderBy('r.date', 'desc')
        ;

        return $qb->getQuery()->getResult();
    }
}