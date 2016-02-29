<?php


namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Repository\Report\RtbReport\AbstractReportRepository;

class RonAdSlotReportRepository extends AbstractReportRepository implements RonAdSlotReportRepositoryInterface
{
    public function getReportForRonAdSlot(RonAdSlotInterface $ronAdSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->leftJoin('r.ronAdSlot', 'sl')
            ->andWhere('r.ronAdSlot = :ron_ad_slot')
            ->andWhere('r.segment IS NULL')
            ->setParameter('ron_ad_slot', $ronAdSlot)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getReportForRonSegment(RonAdSlotInterface $ronAdSlot, SegmentInterface $segment, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->leftJoin('r.ronAdSlot', 'sl')
            ->leftJoin('r.segment', 'sg')
            ->andWhere('r.ronAdSlot = :ron_ad_slot')
            ->andWhere('r.segment = :segment')
            ->setParameter('ron_ad_slot', $ronAdSlot)
            ->setParameter('segment', $segment)
            ->getQuery()
            ->getResult()
            ;
    }
}