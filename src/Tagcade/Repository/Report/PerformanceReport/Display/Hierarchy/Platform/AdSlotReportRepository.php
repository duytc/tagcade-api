<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
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

    public function overrideReport(AdSlotReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform_ad_slot`
                 (ad_slot_id, super_report_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 slot_opportunities, rtb_impressions, billed_rate, custom_rate, billed_amount
                 ) VALUES (:adSlotId, :superReportId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :slotOpportunities, :rtbImpressions, :billedRate, :customRate, :billedAmount
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 fill_rate = :impressions / :totalOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 slot_opportunities = :slotOpportunities,
                 rtb_impressions = :rtbImpressions,
                 billed_rate = :billedRate,
                 custom_rate = :customRate,
                 billed_amount = :billedAmount
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adSlotId', $report->getAdSlot()->getId(), Type::INTEGER);
        $qb->bindValue('superReportId', $report->getSuperReportId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
        $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);
        $qb->bindValue('slotOpportunities', $report->getSlotOpportunities() !== null ? $report->getSlotOpportunities() : 0);
        $qb->bindValue('rtbImpressions', $report->getRtbImpressions() !== null ? $report->getRtbImpressions() : 0);
        $qb->bindValue('billedRate', $report->getBilledRate() !== null ? $report->getBilledRate() : 0);
        $qb->bindValue('customRate', $report->getCustomRate() !== null ? $report->getCustomRate() : 0);
        $qb->bindValue('billedAmount', $report->getBilledAmount() !== null ? $report->getBilledAmount() : 0);

        $connection->beginTransaction();
        try {
            if (false === $qb->execute()) {
                throw new \Exception('Execute error');
            }
            $connection->commit();
        } catch (\Exception $ex) {
            $connection->rollBack();
            throw $ex;
        }
    }
}