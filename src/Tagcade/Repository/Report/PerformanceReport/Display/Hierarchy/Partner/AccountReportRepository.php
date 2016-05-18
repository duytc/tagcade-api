<?php


namespace Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner;

use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;

class AccountReportRepository extends AbstractReportRepository implements AccountReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor(PublisherInterface $publisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.publisher = :publisher')
            ->setParameter('publisher', $publisher)
        ;

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function override(AccountReportInterface $report)
    {
        $sql = 'INSERT INTO `report_performance_display_hierarchy_partner_account`
                (date, est_cpm, est_revenue, fill_rate, impressions, name, passbacks, publisher_id, total_opportunities)
                VALUES (:date, :estCpm, :estRevenue, :fillRate, :impressions, :name, :passbacks, :publisherId, :totalOpportunities)
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

        $qb->bindValue('publisherId', $report->getPublisher()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('estCpm', $report->getEstCpm(), Type::FLOAT);
        $qb->bindValue('estRevenue', $report->getEstRevenue(), Type::FLOAT);
        $qb->bindValue('fillRate', $report->getFillRate(), Type::FLOAT);
        $qb->bindValue('impressions', $report->getImpressions(), Type::INTEGER);
        $qb->bindValue('passbacks', $report->getPassbacks(), Type::INTEGER);
        $qb->bindValue('name', $report->getName());
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