<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class SiteReportRepository extends AbstractReportRepository implements SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sup')
            ->andWhere('r.site = :site')
            ->andWhere('sup.adNetwork = :ad_network')
            ->setParameter('site', $site)
            ->setParameter('ad_network', $adNetwork)
            ->getQuery()
            ->getResult();
    }

    public function getSiteReportForAllPartners(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        // todo: remove
        return [];
    }

    public function getTopSitesByBilledAmount()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.site, sum(s.billedAmount) as billedAmount')
            ->groupBy('s.site')
            ->orderBy('billedAmount', 'DESC');

        return $qb->getQuery()->getResult();
    }
}