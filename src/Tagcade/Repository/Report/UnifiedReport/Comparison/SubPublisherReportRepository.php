<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepository as UnifiedSubPublisherReportRepository;

class SubPublisherReportRepository extends UnifiedSubPublisherReportRepository implements SubPublisherReportRepositoryInterface
{
    public function override(SubPublisherReportInterface $report)
    {
        if ($report->getPerformanceSubPublisherReport() === null && $report->getUnifiedSubPublisherReport() === null) {
            return true;
        }

        $id = $this->getExistingReportId($report);
        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_sub_publisher`
                SET `date` = :date,
                `name` = :name,
                performance_sub_publisher_report_id = :performanceSubPublisherReportId,
                sub_publisher_id = :subPublisherId,
                unified_sub_publisher_report_id = :unifiedSubPublisherReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_sub_publisher`
                    (sub_publisher_id, date, name, performance_sub_publisher_report_id, unified_sub_publisher_report_id)
                    VALUES (:subPublisherId, :date, :name, :performanceSubPublisherReportId, :unifiedSubPublisherReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('subPublisherId', $report->getSubPublisher()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('performanceSubPublisherReportId', $report->getPerformanceSubPublisherReport() === null ? null : $report->getPerformanceSubPublisherReport()->getId());
        $qb->bindValue('unifiedSubPublisherReportId', $report->getUnifiedSubPublisherReport() === null ? null : $report->getUnifiedSubPublisherReport()->getId());

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

    public function getExistingReportId(SubPublisherReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.subPublisher = :subPublisher')
            ->andWhere('r.date = :date')
            ->andWhere('r.performanceSubPublisherReport = :performanceReport OR r.unifiedSubPublisherReport = :unifiedReport')
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('subPublisher', $report->getSubPublisher())
            ->setParameter('performanceReport', $report->getPerformanceSubPublisherReport())
            ->setParameter('unifiedReport', $report->getUnifiedSubPublisherReport())
            ->getQuery()
            ->getOneOrNullResult();
        if ($result instanceof SubPublisherReportInterface) {
            return $result->getId();
        }

        return null;
    }
}