<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoPlatformReportRepository extends AbstractVideoReportRepository implements VideoPlatformReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->getReportsByDateRange($startDate, $endDate);
    }

    public function overrideReport(PlatformReportInterface $report)
    {
        $sql = 'INSERT INTO `video_report_platform_platform_report`
                 (date, ad_tag_bids, ad_tag_errors, ad_tag_requests, bids, bid_rate, billed_amount, billed_rate, blocks, clicks, click_through_rate,
                  errors, error_rate, est_demand_revenue, est_supply_cost, impressions, net_revenue, requests, request_fill_rate)
                 VALUES (:date, :adTagBids, :adTagErrors, :adTagRequests, :bids, :bidRate, :billedAmount, :billedRate, :blocks, :clicks, :clickThroughRate,
                  :errors, :errorRate, :estDemandRevenue, :estSupplyCost, :impressions, :netRevenue, :requests, :requestFillRate)
                 ON DUPLICATE KEY UPDATE
                 ad_tag_bids = :adTagBids,
                 ad_tag_errors = :adTagErrors,
                 ad_tag_requests = :adTagRequests,
                 bids = :bids,
                 bid_rate = :bidRate,
                 blocks = :blocks,
                 clicks = :clicks,
                 click_through_rate = :clickThroughRate,
                 billed_amount = :billedAmount,
                 billed_rate = :billedRate,
                 errors = :errors,
                 error_rate = :errorRate,
                 est_demand_revenue = :estDemandRevenue,
                 est_supply_cost = :estSupplyCost,
                 impressions = :impressions,
                 net_revenue = :netRevenue,
                 requests = :requests,
                 request_fill_rate = :requestFillRate
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('adTagBids', $report->getAdTagBids() !== null ? $report->getAdTagBids() : 0);
        $qb->bindValue('adTagErrors', $report->getAdTagErrors() !== null ? $report->getAdTagErrors() : 0);
        $qb->bindValue('adTagRequests', $report->getAdTagRequests() !== null ? $report->getAdTagRequests() : 0);
        $qb->bindValue('bids', $report->getBids() !== null ? $report->getBids() : 0);
        $qb->bindValue('bidRate', $report->getBidRate() !== null ? $report->getBidRate() : 0);
        $qb->bindValue('blocks', $report->getBlocks() !== null ? $report->getBlocks() : 0);
        $qb->bindValue('clicks', $report->getClicks() !== null ? $report->getClicks() : 0);
        $qb->bindValue('clickThroughRate', $report->getClickThroughRate() !== null ? $report->getClickThroughRate() : 0);
        $qb->bindValue('billedRate', $report->getBilledRate() !== null ? $report->getBilledRate() : 0);
        $qb->bindValue('billedAmount', $report->getBilledAmount() !== null ? $report->getBilledAmount() : 0);
        $qb->bindValue('errors', $report->getErrors() !== null ? $report->getErrors() : 0);
        $qb->bindValue('errorRate', $report->getErrorRate() !== null ? $report->getErrorRate() : 0);
        $qb->bindValue('estDemandRevenue', $report->getEstDemandRevenue() !== null ? $report->getEstDemandRevenue() : 0);
        $qb->bindValue('estSupplyCost', $report->getEstSupplyCost() !== null ? $report->getEstSupplyCost() : 0);
        $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0);
        $qb->bindValue('netRevenue', $report->getNetRevenue() !== null ? $report->getNetRevenue() : 0);
        $qb->bindValue('requests', $report->getRequests() !== null ? $report->getRequests() : 0);
        $qb->bindValue('requestFillRate', $report->getRequestFillRate() !== null ? $report->getRequestFillRate() : 0);

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