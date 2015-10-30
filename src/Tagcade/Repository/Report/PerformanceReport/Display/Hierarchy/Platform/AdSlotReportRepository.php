<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\ReportableAdSlotInterface;
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
}