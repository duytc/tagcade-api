<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class PlatformReportRepository extends AbstractReportRepository implements PlatformReportRepositoryInterface
{
    public function getReportFor(DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSumBilledAmountForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.billedAmount) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getStatsSummaryForDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.totalOpportunities) as totalOpportunities, SUM(r.slotOpportunities) as slotOpportunities, SUM(r.impressions) as impressions,SUM(r.estRevenue) as totalEstRevenue, SUM(r.billedAmount) as totalBilledAmount')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    public function overrideReport(PlatformReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform`
                 (date, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 slot_opportunities, billed_rate, billed_amount, rtb_impressions
                 ) VALUES (:date, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :slotOpportunities, :billedRate, :billedAmount, :rtbImpressions
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 fill_rate = :impressions / :totalOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 slot_opportunities = :slotOpportunities,
                 billed_rate = :billedRate,
                 billed_amount = :billedAmount,
                 rtb_impressions = :rtbImpressions
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
        $qb->bindValue('slotOpportunities', $report->getSlotOpportunities() !== null ? $report->getSlotOpportunities() : 0);
        $qb->bindValue('billedRate', $report->getBilledRate() !== null ? $report->getBilledRate() : 0);
        $qb->bindValue('billedAmount', $report->getBilledAmount() !== null ? $report->getBilledAmount() : 0);
        $qb->bindValue('rtbImpressions', $report->getRtbImpressions() !== null ? $report->getRtbImpressions() : 0);

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