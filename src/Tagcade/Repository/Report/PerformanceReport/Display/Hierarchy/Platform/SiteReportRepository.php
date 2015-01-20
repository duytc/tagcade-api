<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
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
}