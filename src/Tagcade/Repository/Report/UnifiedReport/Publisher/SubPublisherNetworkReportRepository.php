<?php


namespace Tagcade\Repository\Report\UnifiedReport\Publisher;


use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class SubPublisherNetworkReportRepository extends AbstractReportRepository implements SubPublisherNetworkReportRepositoryInterface
{
    public function getReportFor(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.subPublisher = :subPublisher')
            ->setParameter('subPublisher', $subPublisher)
            ->andWhere('r.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork)
        ;

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function saveMultipleReport(array $reports, $batchSize = null)
    {
        if ($batchSize === null) {
            $batchSize = self::BATCH_SIZE;
        }

        $sql = 'INSERT INTO `unified_report_publisher_sub_publisher_network`
                (ad_network_id, sub_publisher_id, date, name, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks)
                VALUES (:adNetworkId, :subPublisherId, :date, :name, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks)
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
            if (!$report instanceof SubPublisherNetworkReport) {
                continue;
            }
            $qb->bindValue('subPublisherId', $report->getSubPublisherId(), Type::INTEGER);
            $qb->bindValue('adNetworkId', $report->getAdNetworkId(), Type::INTEGER);
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
}