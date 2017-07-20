<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\Core\AdTagInterface;

class AdTagReportRepository extends AbstractReportRepository implements AdTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(AdTagInterface $adTag, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.adTag = :ad_tag')
            ->setParameter('ad_tag', $adTag)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(AdTagReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform_ad_tag`
                 (ad_tag_id, super_report_id, date, name, position, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 relative_fill_rate, first_opportunities, verified_impressions, unverified_impressions, blank_impressions, void_impressions, clicks, ad_opportunities
                 ) VALUES (:adTagId, :superReportId, :date, :name, :position, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :relativeFillRate, :firstOpportunities, :verifiedImpressions, :unverifiedImpression, :blankImpressions, :voidImpressions, :clicks, :adOpportunities
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 fill_rate = :impressions / :totalOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 position = :position,
                 relative_fill_rate = :relativeFillRate,
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

        $qb->bindValue('adTagId', $report->getAdTag()->getId(), Type::INTEGER);
        $qb->bindValue('superReportId', $report->getSuperReportId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('position', $report->getPosition());
        $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
        $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
        $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);
        $qb->bindValue('relativeFillRate', $report->getRelativeFillRate() !== null ? $report->getRelativeFillRate() : 0);
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