<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReportInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class SiteReportRepository extends AbstractReportRepository implements SiteReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(SiteInterface $site, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.superReport', 'sup')
            ->andWhere('r.site = :site')
            ->andWhere('sup.adNetwork = :ad_network')
            ->setParameter('site', $site)
            ->setParameter('ad_network', $adNetwork)
            ->getQuery()
            ->getResult();
    }

    public function getTopSitesByBilledAmount()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.site, sum(s.billedAmount) as billedAmount')
            ->groupBy('s.site')
            ->orderBy('billedAmount', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function overrideReport(AdNetworkReportInterface $superReport, SiteReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_ad_network_site`
                 (site_id, super_report_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 first_opportunities, verified_impressions, unverified_impressions, blank_impressions, void_impressions, clicks, ad_opportunities, network_opportunity_fill_rate,
                 in_banner_impressions, in_banner_requests, in_banner_timeouts
                 ) VALUES (:siteId, :superReportId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :firstOpportunities, :verifiedImpressions, :unverifiedImpression, :blankImpressions, :voidImpressions, :clicks, :adOpportunities, :networkOpportunityFillRate,
                  :inBannerImpressions, :inBannerRequests, :inBannerTimeouts
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
                 ad_opportunities = :adOpportunities,
                 network_opportunity_fill_rate = :networkOpportunityFillRate,
                 in_banner_impressions = :inBannerImpressions,
                 in_banner_requests = :inBannerRequests,
                 in_banner_timeouts = :inBannerTimeouts
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('siteId', $report->getSiteId(), Type::INTEGER);
        $qb->bindValue('superReportId', $superReport->getId(), Type::INTEGER);
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
        $qb->bindValue('networkOpportunityFillRate', $report->getNetworkOpportunityFillRate() !== null ? $report->getNetworkOpportunityFillRate() : 0);
        $qb->bindValue('inBannerImpressions', $report->getInBannerImpressions() !== null ? $report->getInBannerImpressions() : 0);
        $qb->bindValue('inBannerRequests', $report->getInBannerRequests() !== null ? $report->getInBannerRequests() : 0);
        $qb->bindValue('inBannerTimeouts', $report->getInBannerTimeouts() !== null ? $report->getInBannerTimeouts() : 0);

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