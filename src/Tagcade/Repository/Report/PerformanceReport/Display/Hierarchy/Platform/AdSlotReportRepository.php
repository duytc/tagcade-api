<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AdSlotReportRepository extends AbstractReportRepository implements AdSlotReportRepositoryInterface
{
    public function getReportFor(ReportableAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->leftJoin('r.adSlot', 'sl')
            ->andWhere('r.adSlot = :ad_slot')
            ->setParameter('ad_slot', $adSlot)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllReportInRange(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sr')
            ->join('sr.superReport', 'pr')
            ->join('pr.publisher', 'p')
            ->andWhere('p.enabled = true')
            ->getQuery()->getResult();
    }

    public function getAllReportInRangeForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sr')
            ->join('sr.superReport', 'pr')
            ->andWhere('pr.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->getQuery()->getResult();
    }
}