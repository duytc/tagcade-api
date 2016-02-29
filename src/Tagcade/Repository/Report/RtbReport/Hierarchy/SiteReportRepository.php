<?php


namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\RtbReport\AbstractReportRepository;

class SiteReportRepository extends AbstractReportRepository implements SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->andWhere('r.site = :site')
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult()
        ;
    }
}