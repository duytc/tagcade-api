<?php


namespace Tagcade\Repository\Report\HeaderBiddingReport\Hierarchy\Platform;

use DateTime;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Repository\Report\HeaderBiddingReport\AbstractReportRepository;

class AdSlotReportRepository extends AbstractReportRepository implements AdSlotReportRepositoryInterface
{
    public function getReportFor(BaseAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate)
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