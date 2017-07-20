<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AdNetworkReportRepository extends AbstractReportRepository implements AdNetworkReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adNetwork = :ad_network')
            ->setParameter('ad_network', $adNetwork);

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getReportForAllAdNetworkOfPublisher(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->join('r.adNetwork', 'nw')
            ->andWhere('nw.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher)
            ->groupBy('r.date');

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(AdNetworkReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_ad_network`
                 (ad_network_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 first_opportunities, verified_impressions, unverified_impressions, blank_impressions, void_impressions, clicks, ad_opportunities
                 ) VALUES (:adNetworkId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :firstOpportunities, :verifiedImpressions, :unverifiedImpression, :blankImpressions, :voidImpressions, :clicks, :adOpportunities
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 fill_rate = :impressions / :totalOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 first_opportunities = :firstOpportunities,
                 verified_impressions = :verifiedImpressions,
                 unverified_impressions = :unverifiedImpression,
                 blank_impressions = :blankImpressions,
                 void_impressions = :voidImpressions,
                 clicks = :clicks,
                 ad_opportunities = :adOpportunities
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetworkId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
        $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);
        $qb->bindValue('firstOpportunities', $report->getFirstOpportunities() !== null ? $report->getFirstOpportunities() : 0);
        $qb->bindValue('verifiedImpressions', $report->getVerifiedImpressions() !== null ? $report->getVerifiedImpressions() : 0);
        $qb->bindValue('unverifiedImpression', $report->getUnverifiedImpressions() !== null ? $report->getUnverifiedImpressions() : 0);
        $qb->bindValue('blankImpressions', $report->getBlankImpressions() !== null ? $report->getVoidImpressions() : 0);
        $qb->bindValue('voidImpressions', $report->getVoidImpressions() !== null ? $report->getPassbacks() : 0);
        $qb->bindValue('clicks', $report->getClicks() !== null ? $report->getClicks() : 0);
        $qb->bindValue('adOpportunities', $report->getAdOpportunities() !== null ? $report->getAdOpportunities() : 0);

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