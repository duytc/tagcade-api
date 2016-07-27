<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
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

        if ($override === true) {
            return $this->overrideReports($reports);
        }

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

    protected function overrideReports(array $reports)
    {
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

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);
        $count = 0;
        $adjustedCommonReports = [];
        foreach($reports as $report) {
            if (!$report instanceof NetworkAdTagReport) {
                continue;
            }

            $adjustedCommonReport = $this->createAdjustedCommonReport($report);
            if ($adjustedCommonReport instanceof CommonReport) {
                $adjustedCommonReports[] = $adjustedCommonReport;
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
            if ($count === 0 || ($count > 0 && $count % self::BATCH_SIZE === 0) ) {
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
            if ($count > 0 && $count % self::BATCH_SIZE === 0) {
                try {
                    $connection->commit();
                } catch (\Exception $ex) {
                    throw $ex;
                }
            }
        }
        // last commit if not reaching BATCH_SIZE again at the end
        if ($count % self::BATCH_SIZE !== 0) {
            try {
                $connection->commit();
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        return $adjustedCommonReports;
    }

    /**
     * Create common report that is the difference between current report and the one in database. This common report will be used to aggregate to higher
     * level reports in order to update changes
     *
     * @param NetworkAdTagReport $report
     * @return mixed|CommonReport
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function createAdjustedCommonReport(NetworkAdTagReport $report)
    {
        $result = $this->createQueryBuilder('r')
            ->addSelect('(:impressions - r.impressions) as impressions')
            ->addSelect('(:totalOpportunities - r.totalOpportunities) as totalOpportunities')
            ->addSelect('(:passbacks - r.passbacks) as passbacks')
            ->addSelect('(:estRevenue - r.estRevenue) as estRevenue')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.date = :date')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('partnerTagId', $report->getPartnerTagId())
            ->setParameter('date', $report->getDate())
            ->setParameter('impressions', $report->getImpressions())
            ->setParameter('totalOpportunities', $report->getTotalOpportunities())
            ->setParameter('passbacks', $report->getPassbacks())
            ->setParameter('estRevenue', $report->getEstRevenue())
            ->getQuery()->getOneOrNullResult();

        $commonReport = new CommonReport();
        $commonReport
            ->setPublisher($report->getAdNetwork()->getPublisher())
            ->setAdNetwork($report->getAdNetwork())
            ->setAdTagId($report->getPartnerTagId())
            ->setDate($report->getDate())
        ;

        // if there are records already existed, common report's values is the differences
        if ($result !== null) {
            $commonReport
                ->setImpressions(filter_var($result['impressions'], FILTER_VALIDATE_INT))
                ->setOpportunities(filter_var($result['totalOpportunities'], FILTER_VALIDATE_INT))
                ->setPassbacks(filter_var($result['passbacks'], FILTER_VALIDATE_INT))
                ->setEstRevenue(filter_var($result['estRevenue'], FILTER_VALIDATE_FLOAT))
            ;
        }
        else { // if the record is not yet existed then the common report's value is it-self
            $commonReport
                ->setImpressions($report->getImpressions())
                ->setOpportunities($report->getTotalOpportunities())
                ->setPassbacks($report->getPassbacks())
                ->setEstRevenue($report->getEstRevenue())
                ->setEstCpm($report->getEstCpm())
                ->setFillRate($report->getFillRate())
            ;
        }

        return $commonReport;
    }
}