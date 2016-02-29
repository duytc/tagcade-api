<?php


namespace Tagcade\Repository\Report\RtbReport\Hierarchy;

use DateTime;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Repository\Report\RtbReport\AbstractReportRepository;

class AdSlotReportRepository extends AbstractReportRepository implements AdSlotReportRepositoryInterface
{
    public function getReportFor(ReportableAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRangeQuery($startDate, $endDate)
            ->leftJoin('r.adSlot', 'sl')
            ->andWhere('r.adSlot = :ad_slot')
            ->setParameter('ad_slot', $adSlot)
            ->getQuery()
            ->getResult()
        ;
    }
}