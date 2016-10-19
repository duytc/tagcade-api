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

    public function getTotalVideoImpressionForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.site', 'st');

        $result = $qb
            ->select('SUM(r.videoAdImpressions) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('st.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('$publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getTotalVideoVisitForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');
        $qb->leftJoin('r.site', 'st');

        $result = $qb
            ->select('SUM(r.visits) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('st.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('$publisher', $publisher->getUser())
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return 0;
        }

        return $result;
    }

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

    public function getBillingReportForPublisherByDay(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.site', 'st');

        return $qb
            ->select('SUM(r.visits) as visits')
            ->addSelect('SUM(r.videoAdImpressions) as videoAdImpressions')
            ->addSelect('SUM(r.billedAmount) as billedAmount')
            ->addSelect('(SUM(r.billedRate * r.billedAmount) / SUM(r.billedAmount)) as billedRate')
            ->addSelect('r.date as date')
            ->addSelect('AVG(r.visits) as averageVisits')
            ->addSelect('AVG(r.videoAdImpressions) as averageVideoAdImpressions')
            ->addSelect('AVG(r.billedAmount) as averageBilledAmount')
            ->addGroupBy('r.date')
            ->where('st.publisher = :publisher')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('publisher', $publisher)
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()->getScalarResult();
    }

    public function getBillingReportForPublisherBySite(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.site', 'st');

        return $qb
            ->select('SUM(r.visits) as visits')
            ->addSelect('SUM(r.videoAdImpressions) as videoAdImpressions')
            ->addSelect('SUM(r.billedAmount) as billedAmount')
            ->addSelect('(SUM(r.billedRate * r.billedAmount) / SUM(r.billedAmount)) as billedRate')
            ->addSelect('AVG(r.visits) as averageVisits')
            ->addSelect('AVG(r.videoAdImpressions) as averageVideoAdImpressions')
            ->addSelect('AVG(r.billedAmount) as averageBilledAmount')
            ->addSelect('st.name as site')
            ->addGroupBy('r.site')
            ->where('st.publisher = :publisher')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('publisher', $publisher)
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()->getScalarResult();
    }

    public function getBillingReportForPlatformByPublisher(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.site', 'st');

        return $qb
            ->leftJoin('st.publisher', 'p')
            ->select('SUM(r.visits) as visits')
            ->addSelect('SUM(r.videoAdImpressions) as videoAdImpressions')
            ->addSelect('SUM(r.billedAmount) as billedAmount')
            ->addSelect('(SUM(r.billedRate * r.billedAmount) / SUM(r.billedAmount)) as billedRate')
            ->addSelect('AVG(r.visits) as averageVisits')
            ->addSelect('AVG(r.videoAdImpressions) as averageVideoAdImpressions')
            ->addSelect('AVG(r.billedAmount) as averageBilledAmount')
            ->addSelect('p.username as publisher')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->addGroupBy('st.publisher')
            ->getQuery()->getScalarResult();
    }

    public function getBillingReportForPlatformByDay(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->select('SUM(r.visits) as visits')
            ->addSelect('SUM(r.videoAdImpressions) as videoAdImpressions')
            ->addSelect('SUM(r.billedAmount) as billedAmount')
            ->addSelect('(SUM(r.billedRate * r.billedAmount) / SUM(r.billedAmount)) as billedRate')
            ->addSelect('AVG(r.visits) as averageVisits')
            ->addSelect('AVG(r.videoAdImpressions) as averageVideoAdImpressions')
            ->addSelect('AVG(r.billedAmount) as averageBilledAmount')
            ->addSelect('r.date as date')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->addGroupBy('r.date')
            ->getQuery()->getScalarResult();
    }
}