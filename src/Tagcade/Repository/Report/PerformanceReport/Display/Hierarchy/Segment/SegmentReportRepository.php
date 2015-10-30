<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class SegmentReportRepository extends AbstractReportRepository implements SegmentReportRepositoryInterface
{
    public function getReportFor(SegmentInterface $segment, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.segment = :segment')
            ->setParameter('segment', $segment->getId(), TYPE::INTEGER)
            ->getQuery()
            ->getResult()
            ;
    }
}