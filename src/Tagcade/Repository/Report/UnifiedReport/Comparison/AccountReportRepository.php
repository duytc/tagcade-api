<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\AccountReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepository as UnifiedAccountReportRepository;

class AccountReportRepository extends UnifiedAccountReportRepository implements AccountReportRepositoryInterface
{
    public function override(AccountReportInterface $report)
    {
        if ($report->getPerformanceAccountReport() === null && $report->getUnifiedAccountReport() === null) {
            return true;
        }

        $id = $this->getExistingReportId($report);

        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_account`
                SET `date` = :date,
                `name` = :name,
                performance_account_report_id = :performanceAccountReportId,
                publisher_id = :publisherId,
                unified_account_report_id = :unifiedAccountReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_account`
                    (date, name, performance_account_report_id, publisher_id, unified_account_report_id)
                    VALUES (:date, :name, :performanceAccountReportId, :publisherId, :unifiedAccountReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('publisherId', $report->getPublisher()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('performanceAccountReportId', $report->getPerformanceAccountReport() === null ? null : $report->getPerformanceAccountReport()->getId());
        $qb->bindValue('unifiedAccountReportId', $report->getUnifiedAccountReport() === null ? null : $report->getUnifiedAccountReport()->getId());

        if (is_int($id)) {
            $qb->bindValue('id', $id);
        }

        try {
            $connection->beginTransaction();
            $qb->execute();
            $connection->commit();
        } catch (\Exception $ex) {
            throw $ex;
        }

        return true;
    }

    /**
     * if an record already existed with the given compound unique key then return the record's id
     * otherwise return null
     * @param AccountReportInterface $report
     * @return null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExistingReportId(AccountReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.date = :date')
            ->andWhere('r.performanceAccountReport = :performanceReport OR r.unifiedAccountReport = :unifiedReport')
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('performanceReport', $report->getPerformanceAccountReport())
            ->setParameter('unifiedReport', $report->getUnifiedAccountReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AccountReportInterface) {
            return $result->getId();
        }

        return null;
    }
}