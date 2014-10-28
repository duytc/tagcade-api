<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\Core\AdSlotInterface;

class AdSlotReportRepository extends AbstractReportRepository implements AdSlotReportRepositoryInterface
{
    public function getReportFor(AdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adSlot = :ad_slot')
            ->setParameter('ad_slot', $adSlot)
            ->getQuery()
            ->getResult()
        ;
    }
}