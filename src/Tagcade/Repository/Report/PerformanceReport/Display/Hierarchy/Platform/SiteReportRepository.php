<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Model\Core\SiteInterface;

class SiteReportRepository extends AbstractReportRepository implements SiteReportRepositoryInterface
{
    public function getReportFor(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.site = :site')
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSumBilledAmountForSite(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('st');

        $result = $qb
            ->select('SUM(st.billedAmount) as total')
            ->where('st.site = :site')
            ->andWhere($qb->expr()->between('st.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $startDate, Type::DATE)
            ->setParameter('end_date', $endDate, Type::DATE)
            ->setParameter('site', $site)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (null === $result) {
            return 0;
        }

        return $result;
    }

    public function getSumSlotOpportunities(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.slotOpportunities) as total')
            ->where('r.site = :site')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('site', $site)
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

    public function getSumSlotHbRequests(SiteInterface $site, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->createQueryBuilder('r');

        $result = $qb
            ->select('SUM(r.hbRequests) as total')
            ->where('r.site = :site')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('site', $site)
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


    public function getTopSitesByBilledAmount(DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb->select('s.id, SUM(sr.billedAmount) AS totalBilledAmount')
            ->join('sr.site', 's')
            ->where($qb->expr()->between('sr.date', ':startDate', ':endDate'))
            ->andWhere('s.id = sr.site')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->groupBy('sr.site')
            ->orderBy('totalBilledAmount', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getTopSitesForPublisherByEstRevenue(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $limit = 10)
    {
        $qb = $this->createQueryBuilder('sr');
        $qb->select('s.id, SUM(sr.estRevenue) AS totalEstRevenue')
            ->join('sr.site', 's')
            ->join('s.publisher', 'p')
            ->where($qb->expr()->between('sr.date', ':startDate', ':endDate'))
            ->andWhere('s.id = sr.site')
            ->andWhere('p.id = :publisherId')
            ->setParameter('startDate', $startDate, Type::DATE)
            ->setParameter('endDate', $endDate, Type::DATE)
            ->setParameter('publisherId', $publisher->getId(), Type::INTEGER)
            ->groupBy('sr.site')
            ->orderBy('totalEstRevenue', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    public function overrideReport(SiteReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_platform_site`
                 (site_id, super_report_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks,
                 slot_opportunities, billed_rate, billed_amount, rtb_impressions, in_banner_requests, in_banner_impressions, in_banner_timeouts, in_banner_billed_rate, in_banner_billed_amount
                 ) VALUES (:siteId, :superReportId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks,
                  :slotOpportunities, :billedRate, :billedAmount, :rtbImpressions, :inBannerRequests, :inBannerImpressions, :inBannerTimeouts, :inBannerBilledRate, :inBannerBilledAmount
                 ) ON DUPLICATE KEY UPDATE
                 est_revenue = :estRevenue,
                 impressions = :impressions,
                 total_opportunities = :totalOpportunities,
                 passbacks = :passbacks,
                 fill_rate = :impressions / :totalOpportunities,
                 est_cpm = 1000 * :estRevenue / :impressions,
                 slot_opportunities = :slotOpportunities,
                 in_banner_requests = :inBannerRequests,
                 in_banner_impressions = :inBannerImpressions,
                 in_banner_timeouts = :inBannerTimeouts,
                 in_banner_billed_amount = :inBannerBilledAmount,
                 in_banner_billed_rate = :inBannerBilledRate,
                 billed_rate = :billedRate,
                 billed_amount = :billedAmount,
                 rtb_impressions = :rtbImpressions
                 ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('siteId', $report->getSite()->getId(), Type::INTEGER);
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
        $qb->bindValue('inBannerRequests', $report->getInBannerRequests() !== null ? $report->getInBannerRequests() : 0);
        $qb->bindValue('inBannerImpressions', $report->getInBannerImpressions() !== null ? $report->getInBannerImpressions() : 0);
        $qb->bindValue('inBannerTimeouts', $report->getInBannerTimeouts() !== null ? $report->getInBannerTimeouts() : 0);
        $qb->bindValue('inBannerBilledAmount', $report->getInBannerBilledAmount() !== null ? $report->getInBannerBilledAmount() : 0);
        $qb->bindValue('inBannerBilledRate', $report->getInBannerBilledRate() !== null ? $report->getInBannerBilledRate() : 0);
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