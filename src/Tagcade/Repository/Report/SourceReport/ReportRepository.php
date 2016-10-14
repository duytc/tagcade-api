<?php

namespace Tagcade\Repository\Report\SourceReport;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

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
            ->where('r.site = :site')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('site', $site)
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->orderBy('r.date', 'desc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */

    public function getTotalVideoImpressionForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {

        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.videoAdImpressions) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.site = :site')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('site', $site)
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getTotalVideoVisitForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {

        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.visits) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.site = :site')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('site', $site)
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return 0;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSourceReportsForPublisher(PublisherInterface $publisher, DateTime $dateTime)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.site', 'st');

        return $qb
                ->select('r, st')
                ->where($qb->expr()->eq('r.date',':date_time'))
                ->andWhere('st.publisher = :publisher')
                ->setParameter('date_time', $dateTime->format('Y-m-d'))
                ->setParameter('publisher', $publisher->getUser())
                ->getQuery()
                ->getResult();
    }
}