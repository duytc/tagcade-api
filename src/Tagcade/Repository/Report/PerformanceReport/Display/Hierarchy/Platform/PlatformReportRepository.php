<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSumBilledAmountForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.billedAmount) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getStatsSummaryForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.totalOpportunities) as totalOpportunities, SUM(r.slotOpportunities) as slotOpportunities, SUM(r.impressions) as impressions,SUM(r.estRevenue) as totalEstRevenue, SUM(r.billedAmount) as totalBilledAmount')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(PlatformReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform`
                 (date, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks, ad_opportunities, supply_cost, est_profit, opportunity_fill_rate,
                 slot_opportunities, billed_rate, billed_amount, in_banner_requests, in_banner_impressions, in_banner_timeouts, in_banner_billed_rate, in_banner_billed_amount
                 ) VALUES (:date, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks, :adOpportunities, :supply_cost, :est_profit, :opportunityFillRate,
                  :slotOpportunities, :billedRate, :billedAmount, :inBannerRequests, :inBannerImpressions, :inBannerTimeouts, :inBannerBilledRate, :inBannerBilledAmount
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 ad_opportunities = :adOpportunities,
                 supply_cost= :supply_cost,
                 est_profit = :est_profit,
                 opportunity_fill_rate = :opportunityFillRate,
                 fill_rate = :impressions / :slotOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 slot_opportunities = :slotOpportunities,
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

        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
        $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);
        $qb->bindValue('adOpportunities', $report->getAdOpportunities() !== null ? $report->getAdOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('supply_cost', $report->getSupplyCost() !== null ? $report->getSupplyCost() : 0);
        $qb->bindValue('est_profit', $report->getEstProfit() !== null ? $report->getEstProfit() : 0);
        $qb->bindValue('opportunityFillRate', $report->getOpportunityFillRate() !== null ? $report->getOpportunityFillRate() : 0, Type::DECIMAL);
        $qb->bindValue('slotOpportunities', $report->getSlotOpportunities() !== null ? $report->getSlotOpportunities() : 0);
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