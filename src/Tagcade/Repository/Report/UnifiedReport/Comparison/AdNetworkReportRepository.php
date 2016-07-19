<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepository as UnifiedAdNetworkReportRepository;

class AdNetworkReportRepository extends UnifiedAdNetworkReportRepository implements AdNetworkReportRepositoryInterface
{
    public function override(AdNetworkReportInterface $report)
    {
        if ($report->getPerformanceAdNetworkReport() === null && $report->getUnifiedAdNetworkReport() === null) {
            throw new RuntimeException('both Performance and Unified Report can not be null');
        }

        $id = $this->getExistingReportId($report);
        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_ad_network`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                performance_ad_network_report_id = :performanceAdNetworkReportId,
                unified_ad_network_report_id = :unifiedAdNetworkReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_ad_network`
                    (ad_network_id, date, name, performance_ad_network_report_id, unified_ad_network_report_id)
                    VALUES (:adNetworkId, :date, :name, :performanceAdNetworkReportId, :unifiedAdNetworkReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('performanceAdNetworkReportId', $report->getPerformanceAdNetworkReport() === null ? null : $report->getPerformanceAdNetworkReport()->getId());
        $qb->bindValue('unifiedAdNetworkReportId', $report->getUnifiedAdNetworkReport() === null ? null : $report->getUnifiedAdNetworkReport()->getId());

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

    public function getExistingReportId(AdNetworkReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.date = :date')
            ->andWhere('r.performanceAdNetworkReport = :performanceReport OR r.unifiedAdNetworkReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('performanceReport', $report->getPerformanceAdNetworkReport())
            ->setParameter('unifiedReport', $report->getUnifiedAdNetworkReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AdNetworkReportInterface) {
            return $result->getId();
        }

        return null;
    }
}