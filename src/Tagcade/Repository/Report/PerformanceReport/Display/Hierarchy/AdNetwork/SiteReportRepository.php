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
            ->getResult()
        ;
    }
}