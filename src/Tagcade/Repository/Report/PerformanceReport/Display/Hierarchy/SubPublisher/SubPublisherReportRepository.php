<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class SubPublisherReportRepository extends AbstractReportRepository implements SubPublisherReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.subPublisher = :subPublisher')
            ->setParameter('subPublisher', $subPublisher)
        ;

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function override(SubPublisherReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_sub_publisher`
                (date, est_cpm, est_revenue, fill_rate, impressions, passbacks, sub_publisher_id, total_opportunities)
                VALUES (:date, :estCpm, :estRevenue, :fillRate, :impressions, :passbacks, :subPublisherId, :totalOpportunities)
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

        $qb->bindValue('subPublisherId', $report->getSubPublisher()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('estCpm', $report->getEstCpm(), Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue(), Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate(), Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions(), Type::INTEGER);
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