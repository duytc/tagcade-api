<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

class NetworkAdTagReportRepository extends AbstractReportRepository implements NetworkAdTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor($adNetwork, $partnerTagId, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->setParameter('partnerTagId', $partnerTagId);

        if ($adNetwork instanceof AdNetworkInterface) {
            $qb
            ->andWhere('r.adNetwork = :ad_network')
            ->setParameter('ad_network', $adNetwork);
        }

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function saveMultipleReport(array $reports, $override = false, $batchSize = null)
    {
        if ($batchSize === null) {
            $batchSize = self::BATCH_SIZE;
        }

        if ($override === false) {
            $sql = 'INSERT INTO `unified_report_network_ad_tag`
                    (ad_network_id, partner_tag_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks)
                    VALUES (:adNetworkId, :partnerTagId, :date, :name,:estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks)
                    ON DUPLICATE KEY UPDATE
                    est_revenue = est_revenue + :estRevenue,
                    impressions = impressions + :impressions,
                    total_opportunities = total_opportunities + :totalOpportunities,
                    passbacks = passbacks + :passbacks,
                    fill_rate = (impressions + :impressions) / (total_opportunities + :totalOpportunities),
                    est_cpm = 1000 * (est_revenue + :estRevenue) / (impressions + :impressions)
                    ';
        } else {
            $sql = 'INSERT INTO `unified_report_network_ad_tag`
                    (ad_network_id, partner_tag_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks)
                    VALUES (:adNetworkId, :partnerTagId, :date, :name,:estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks)
                    ON DUPLICATE KEY UPDATE
                    est_revenue = :estRevenue,
                    impressions = :impressions,
                    total_opportunities = :totalOpportunities,
                    passbacks = :passbacks,
                    fill_rate = :impressions / :totalOpportunities,
                    est_cpm = 1000 * :estRevenue / :impressions
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);
        $count = 0;
        foreach($reports as $report) {
            if (!$report instanceof NetworkAdTagReport) {
                continue;
            }
            $qb->bindValue('adNetworkId', $report->getAdNetworkId(), Type::INTEGER);
            $qb->bindValue('partnerTagId', $report->getPartnerTagId(), Type::STRING);
            $qb->bindValue('date', $report->getDate(), Type::DATE);
            $qb->bindValue('name', $report->getName());
            $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
            $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
            $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
            $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
            $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
            $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);
            ;
            // begin transaction when start loop or after reaching BATCH_SIZE
            if ($count === 0 || ($count > 0 && $count % $batchSize === 0) ) {
                $connection->beginTransaction();
            }
            try {
                if (false === $qb->execute()) {
                    throw new \Exception('Execute error');
                }
            } catch (\Exception $ex) {
                throw $ex;
            }
            $count++;
            // commit if reach BATCH_SIZE
            if ($count > 0 && $count % $batchSize === 0) {
                try {
                    $connection->commit();
                } catch (\Exception $ex) {
                    throw $ex;
                }
            }
        }
        // last commit if not reaching BATCH_SIZE again at the end
        if ($count % $batchSize !== 0) {
            try {
                $connection->commit();
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        return $count;
    }

    /**
     * @param Params $params
     * @return array
     */
    public function getAllDistinctAdTags(Params $params)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->select('r.partnerTagId')
            ->distinct()
            ->where($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdNetworkInterface $partner
     * @param Params $params
     * @return array
     */
    public function getAllDistinctAdTagsForPartner(AdNetworkInterface $partner, Params $params)
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->select('r.partnerTagId')
            ->distinct()
            ->where('r.adNetwork = :adNetwork')
            ->andWhere($qb->expr()->between('r.date', ':start_date', ':end_date'))
            ->setParameter('start_date', $params->getStartDate(), Type::DATE)
            ->setParameter('end_date', $params->getEndDate(), Type::DATE)
            ->setParameter('adNetwork', $partner)
            ->getQuery()
            ->getResult();
    }
}