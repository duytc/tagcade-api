<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Segment;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class RonAdSlotReportRepository extends AbstractReportRepository implements RonAdSlotReportRepositoryInterface
{
    public function getReportForRonAdSlot(RonAdSlotInterface $ronAdSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.ronAdSlot = :ron_ad_slot')
            ->andWhere('r.segment IS NULL')
            ->setParameter('ron_ad_slot', $ronAdSlot->getId(), TYPE::INTEGER)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param SegmentInterface $segment
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getReportForRonSegment(RonAdSlotInterface $ronAdSlot, SegmentInterface $segment, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.ronAdSlot = :ron_ad_slot')
            ->andWhere('r.segment = :segment')
            ->setParameter('ron_ad_slot', $ronAdSlot->getId(), TYPE::INTEGER)
            ->setParameter('segment', $segment->getId(), TYPE::INTEGER)
            ->getQuery()
            ->getResult()
            ;
    }
}