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
    /**
     * @inheritdoc
     */
    public function getReportFor(ReportableAdSlotInterface $adSlot, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->leftJoin('r.adSlot', 'sl')
            ->andWhere('r.adSlot = :ad_slot')
            ->setParameter('ad_slot', $adSlot)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAllReportInRange(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sr')
            ->join('sr.superReport', 'pr')
            ->join('pr.publisher', 'p')
            ->andWhere('p.enabled = true')
            ->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAllReportInRangeForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sr')
            ->join('sr.superReport', 'pr')
            ->andWhere('pr.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(AdSlotReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform_ad_slot`
                 (ad_slot_id, super_report_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks, ad_opportunities, opportunity_fill_rate,
                 slot_opportunities, refreshed_slot_opportunities, billed_rate, billed_amount, in_banner_requests, in_banner_impressions, in_banner_timeouts, in_banner_billed_rate, in_banner_billed_amount
                 ) VALUES (:adSlotId, :superReportId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks, :adOpportunities, :opportunityFillRate,
                  :slotOpportunities, :refreshedSlotOpportunities, :billedRate, :billedAmount, :inBannerRequests, :inBannerImpressions, :inBannerTimeouts, :inBannerBilledRate, :inBannerBilledAmount
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 ad_opportunities = :adOpportunities,
                 opportunity_fill_rate = :opportunityFillRate,
                 fill_rate = :impressions / :slotOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 slot_opportunities = :slotOpportunities,
                 refreshed_slot_opportunities = :refreshedSlotOpportunities,
                 in_banner_requests = :inBannerRequests,
                 in_banner_impressions = :inBannerImpressions,
                 in_banner_timeouts = :inBannerTimeouts,
                 in_banner_billed_amount = :inBannerBilledAmount,
                 in_banner_billed_rate = :inBannerBilledRate,
                 billed_rate = :billedRate,
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
        $qb->bindValue('adOpportunities', $report->getAdOpportunities() !== null ? $report->getAdOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('opportunityFillRate', $report->getOpportunityFillRate() !== null ? $report->getOpportunityFillRate() : 0, Type::DECIMAL);
        $qb->bindValue('slotOpportunities', $report->getSlotOpportunities() !== null ? $report->getSlotOpportunities() : 0);
        $qb->bindValue('refreshedSlotOpportunities', $report->getRefreshedSlotOpportunities() !== null ? $report->getRefreshedSlotOpportunities() : 0);
        $qb->bindValue('inBannerRequests', $report->getInBannerRequests() !== null ? $report->getInBannerRequests() : 0);
        $qb->bindValue('inBannerImpressions', $report->getInBannerImpressions() !== null ? $report->getInBannerImpressions() : 0);
        $qb->bindValue('inBannerTimeouts', $report->getInBannerTimeouts() !== null ? $report->getInBannerTimeouts() : 0);
        $qb->bindValue('inBannerBilledAmount', $report->getInBannerBilledAmount() !== null ? $report->getInBannerBilledAmount() : 0);
        $qb->bindValue('inBannerBilledRate', $report->getInBannerBilledRate() !== null ? $report->getInBannerBilledRate() : 0);
        $qb->bindValue('billedRate', $report->getBilledRate() !== null ? $report->getBilledRate() : 0);
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