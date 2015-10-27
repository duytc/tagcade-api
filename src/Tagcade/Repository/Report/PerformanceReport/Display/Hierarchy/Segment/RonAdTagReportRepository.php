<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Tagcade\Model\Core\RonAdTagInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class RonAdTagReportRepository extends AbstractReportRepository implements RonAdTagReportRepositoryInterface
{
    public function getReportFor(RonAdTagInterface $ronAdTag, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.ronAdTag = :ron_ad_tag')
            ->andWhere('r.segment IS NULL')
            ->setParameter('ron_ad_tag', $ronAdTag)
            ->getQuery()
            ->getResult()
        ;
    }
}