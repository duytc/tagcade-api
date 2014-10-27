<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AdNetworkReportRepository extends AbstractReportRepository implements AdNetworkReportRepositoryInterface
{
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adNetwork = :ad_network')
            ->setParameter('ad_network', $adNetwork)
            ->getQuery()
            ->getResult()
        ;
    }
}