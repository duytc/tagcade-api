<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\Core\AdTagInterface;

class AdTagReportRepository extends AbstractReportRepository implements AdTagReportRepositoryInterface
{
    public function getReportFor(AdtagInterface $adTag, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adTag = :ad_tag')
            ->setParameter('ad_tag', $adTag)
            ->getQuery()
            ->getResult()
        ;
    }
}