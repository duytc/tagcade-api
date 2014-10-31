<?php

namespace Tagcade\Repository\Report\SourceReport;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use DateTime;
use Tagcade\Entity\Report\SourceReport\Record;
use Tagcade\Entity\Report\SourceReport\Report;
use Tagcade\Model\Core\SiteInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\DBAL\Types\Type;

class ReportRepository extends EntityRepository implements ReportRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function getReport(SiteInterface $site, DateTime $date, $rowOffset = 0, $rowLimit = 200)
    {

        $qb = $this->createQueryBuilder('r')
            ->select('r', 'rec')
            ->join('r.records', 'rec')
            ->Where('r.site = :site')
            ->andwhere('r.date = :date')
            ->setFirstResult($rowOffset)
            ->setMaxResults($rowLimit)
            ->setParameter('site', $site)
            ->setParameter('date', $date, TYPE::DATE);

        //TODO - Find a way to remove this hack, a way to get result with row limit correctly. It seems Paginator bug.
        $re = $qb->getQuery()->getResult();

        return iterator_to_array(new Paginator($qb->getQuery()));
    }
}