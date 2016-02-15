<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\Core\SiteInterface;

class SiteReportRepository extends AbstractReportRepository implements SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.site = :site')
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSumBilledAmountForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('st');

        $result = $qb
            ->select('SUM(st.billedAmount) as total')
            ->where('st.site = :site')
            ->andWhere($qb->expr()->between('st.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('site', $site)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getSumSlotOpportunities(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.slotOpportunities) as total')
            ->where('r.site = :site')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('site', $site)
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getTopSitesByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb->select('s.id, SUM(sr.billedAmount) AS totalBilledAmount')
            ->join('sr.site', 's')
            ->where($qb->expr()->between('sr.date', ':startDate', ':endDate'))
            ->andWhere('s.id = sr.site')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->groupBy('sr.site')
            ->orderBy('totalBilledAmount', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb->select('s.id, SUM(sr.estRevenue) AS totalEstRevenue')
            ->join('sr.site', 's')
            ->join('s.publisher', 'p')
            ->where($qb->expr()->between('sr.date', ':startDate', ':endDate'))
            ->andWhere('s.id = sr.site')
            ->andWhere('p.id = :publisherId')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->setParameter('publisherId', $publisher->getId(), Type::INTEGER)
            ->groupBy('sr.site')
            ->orderBy('totalEstRevenue', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

}