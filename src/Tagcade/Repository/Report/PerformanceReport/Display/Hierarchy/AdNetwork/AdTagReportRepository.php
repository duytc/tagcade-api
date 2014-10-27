<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AdTagReportRepository extends AbstractReportRepository implements AdTagReportRepositoryInterface
{
    public function getReportFor(AdTagInterface $adTag, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adTag = :ad_tag')
            ->setParameter('ad_tag', $adTag)
            ->getQuery()
            ->getResult()
        ;
    }
}