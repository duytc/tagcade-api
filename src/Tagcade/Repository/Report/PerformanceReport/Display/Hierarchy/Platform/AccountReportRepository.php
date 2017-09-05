<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AccountReportRepository extends AbstractReportRepository implements AccountReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAggregatedReportsByDateRange(DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate);
        $qb->join('r.publisher', 'p')
            ->andWhere($qb->expr()->orX('p.testAccount = 0', 'p.testAccount IS NULL'))
            ->andWhere('p.enabled = 1');
        $qb->select('
            SUM(r.totalOpportunities) as totalOpportunities,
            SUM(r.slotOpportunities) as slotOpportunities,
            SUM(r.impressions) as impressions,
            SUM(r.passbacks) as passbacks,
            SUM(r.adOpportunities) as adOpportunities,
            SUM(r.opportunityFillRate) as opportunityFillRate,
            SUM(r.billedAmount) as billedAmount,
            SUM(r.inBannerRequests) as inBannerRequests,
            SUM(r.inBannerBilledAmount) as inBannerBilledAmount,
            SUM(r.inBannerBilledRate) as inBannerBilledRate,
            SUM(r.inBannerTimeouts) as inBannerTimeouts,
            SUM(r.inBannerImpressions) as inBannerImpressions
            '
        );

        return current($qb->getQuery()->getArrayResult());
    }

    /**
     * @inheritdoc
     */
    public function getSumSlotOpportunities(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.slotOpportunities) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
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
    public function getSumSlotInBannerImpressions(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.inBannerImpressions) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
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
    public function getSumBilledAmountForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.billedAmount) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
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
    public function getSumRevenueForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.estRevenue) as total')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
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
    public function getStatsSummaryForPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.totalOpportunities) as totalOpportunities, SUM(r.slotOpportunities) as slotOpportunities, SUM(r.impressions) as impressions, SUM(r.estRevenue) as totalEstRevenue, SUM(r.billedAmount) as totalBilledAmount')
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->andWhere('r.publisher = :publisher')
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('publisher', $publisher->getUser())
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getTopPublishersByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('pr');
        $qb->select('p.id, SUM(pr.billedAmount) AS totalBilledAmount')
            ->join('pr.publisher', 'p')
            ->where($qb->expr()->between('pr.date', ':startDate', ':endDate'))
            ->andWhere('p.id = pr.publisher')
            ->andWhere('p.enabled = 1')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->groupBy('pr.publisher')
            ->orderBy('totalBilledAmount', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(AccountReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform_account`
                 (publisher_id, super_report_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks, ad_opportunities, opportunity_fill_rate,
                 slot_opportunities, billed_rate, billed_amount, in_banner_requests, in_banner_impressions, in_banner_timeouts, in_banner_billed_rate, in_banner_billed_amount
                 ) VALUES (:publisherId, :superReportId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks, :adOpportunities, :opportunityFillRate,
                  :slotOpportunities, :billedRate, :billedAmount, :inBannerRequests, :inBannerImpressions, :inBannerTimeouts, :inBannerBilledRate, :inBannerBilledAmount
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

        $qb->bindValue('publisherId', $report->getPublisherId(), Type::INTEGER);
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