<?php


namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\HeaderBiddingReport\AbstractReportRepository;

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
}