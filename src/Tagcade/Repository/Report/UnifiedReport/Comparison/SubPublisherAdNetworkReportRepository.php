<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepository as UnifiedSubPublisherAdNetworkReportRepository;

class SubPublisherAdNetworkReportRepository extends UnifiedSubPublisherAdNetworkReportRepository implements SubPublisherAdNetworkReportRepositoryInterface
{
    public function override(SubPublisherAdNetworkReportInterface $report)
    {
        $id = $this->getExistingReportId($report);

        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_sub_publisher_ad_network`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                performance_sub_publisher_ad_network_report_id = :performanceSubPublisherAdNetworkReportId,
                sub_publisher_id = :subPublisherId,
                unified_sub_publisher_ad_network_report_id = :unifiedSubPublisherAdNetworkReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_sub_publisher_ad_network`
                    (ad_network_id, sub_publisher_id, date, name, performance_sub_publisher_ad_network_report_id, unified_sub_publisher_ad_network_report_id)
                    VALUES (:adNetworkId, :subPublisherId, :date, :name, :performanceSubPublisherAdNetworkReportId, :unifiedSubPublisherAdNetworkReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('subPublisherId', $report->getSubPublisher()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('performanceSubPublisherAdNetworkReportId', $report->getPerformanceSubPublisherAdNetworkReport() === null ? null : $report->getPerformanceSubPublisherAdNetworkReport()->getId());
        $qb->bindValue('unifiedSubPublisherAdNetworkReportId', $report->getUnifiedSubPublisherAdNetworkReport() === null ? null : $report->getUnifiedSubPublisherAdNetworkReport()->getId());

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

    public function getExistingReportId(SubPublisherAdNetworkReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.subPublisher = :subPublisher')
            ->andWhere('r.date = :date')
            ->andWhere('r.performanceSubPublisherAdNetworkReport = :performanceReport OR r.unifiedSubPublisherAdNetworkReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('subPublisher', $report->getSubPublisher())
            ->setParameter('performanceReport', $report->getPerformanceSubPublisherAdNetworkReport())
            ->setParameter('unifiedReport', $report->getUnifiedSubPublisherAdNetworkReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof SubPublisherAdNetworkReportInterface) {
            return $result->getId();
        }

        return null;
    }
}