<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkAdTagReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepository as UnifiedAdNetworkAdTagReportRepository;

class AdNetworkAdTagReportRepository extends UnifiedAdNetworkAdTagReportRepository implements AdNetworkAdTagReportRepositoryInterface
{
    public function override(AdNetworkAdTagReportInterface $report)
    {
        if ($report->getPerformanceAdNetworkAdTagReport() === null && $report->getUnifiedAdNetworkAdTagReport() === null) {
            return true;
        }

        $id = $this->getExistingReportId($report);

        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_ad_network_ad_tag`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                performance_ad_network_ad_tag_report_id = :performanceAdNetworkAdTagReportId,
                partner_tag_id = :partnerTagId,
                unified_ad_network_ad_tag_report_id = :unifiedAdNetworkAdTagReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_ad_network_ad_tag`
                    (ad_network_id, date, name, performance_ad_network_ad_tag_report_id, partner_tag_id, unified_ad_network_ad_tag_report_id)
                    VALUES (:adNetworkId, :date, :name, :performanceAdNetworkAdTagReportId, :partnerTagId, :unifiedAdNetworkAdTagReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('partnerTagId', $report->getPartnerTagId());
        $qb->bindValue('performanceAdNetworkAdTagReportId', $report->getPerformanceAdNetworkAdTagReport() === null ? null : $report->getPerformanceAdNetworkAdTagReport()->getId());
        $qb->bindValue('unifiedAdNetworkAdTagReportId', $report->getUnifiedAdNetworkAdTagReport() === null ? null : $report->getUnifiedAdNetworkAdTagReport()->getId());

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
     * @param AdNetworkAdTagReportInterface $report
     * @return null | int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getExistingReportId(AdNetworkAdTagReportInterface $report)
    {
        $result =  $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.date = :date')
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.performanceAdNetworkAdTagReport = :performanceReport OR r.unifiedAdNetworkAdTagReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('partnerTagId', $report->getPartnerTagId())
            ->setParameter('performanceReport', $report->getPerformanceAdNetworkAdTagReport())
            ->setParameter('unifiedReport', $report->getUnifiedAdNetworkAdTagReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AdNetworkAdTagReportInterface) {
            return $result->getId();
        }

        return null;
    }
}