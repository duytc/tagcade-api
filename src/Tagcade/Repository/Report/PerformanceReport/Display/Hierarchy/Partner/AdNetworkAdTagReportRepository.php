<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Doctrine\DBAL\Types\Type;

class AdNetworkAdTagReportRepository extends AbstractReportRepository implements AdNetworkAdTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(AdNetworkInterface $adNetwork = null, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->setParameter('partnerTagId', $partnerTagId, Type::STRING);

        // check if get report for a partner, else is for all partners
        if ($adNetwork instanceof AdNetworkInterface) {
            $qb
                ->andWhere('r.adNetwork = :adNetwork_id')
                ->setParameter('adNetwork_id', $adNetwork->getId(), Type::INTEGER)
            ;
        }

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAllReportsFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate)
    {
        return $this->getReportsInRange($startDate, $endDate)
            ->join('r.adNetwork ', 'ad')
            ->andWhere('ad.publisher = :publisher')
            ->setParameter('publisher', $publisher->getId(), Type::INTEGER)
            ->getQuery()
            ->getResult();
    }

    public function override(AdNetworkAdTagReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_partner_ad_network_ad_tag`
                (ad_network_id, date, est_cpm, est_revenue, fill_rate, impressions, name, passbacks, partner_tag_id, total_opportunities)
                VALUES (:adNetworkId, :date, :estCpm, :estRevenue, :fillRate, :impressions, :name, :passbacks, :partnerTagId, :totalOpportunities)
                ON DUPLICATE KEY UPDATE
                est_cpm = :estCpm,
                est_revenue = :estRevenue,
                fill_rate = :fillRate,
                impressions = :impressions,
                passbacks = :passbacks,
                total_opportunities = :totalOpportunities
                ';

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('partnerTagId', $report->getPartnerTagId());
        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('estCpm', $report->getEstCpm(), Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue(), Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate(), Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions(), Type::INTEGER);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('passbacks', $report->getPassbacks(), Type::INTEGER);
        $qb->bindValue('totalOpportunities', $report->getTotalOpportunities(), Type::INTEGER);

        try {
            $connection->beginTransaction();
            $qb->execute();
            $connection->commit();
        } catch (\Exception $ex) {
            throw $ex;
        }

        return true;
    }
}