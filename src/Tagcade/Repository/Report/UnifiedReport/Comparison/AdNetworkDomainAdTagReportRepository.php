<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepository as UnifiedAdNetworkDomainAdTagReportRepository;

class AdNetworkDomainAdTagReportRepository extends UnifiedAdNetworkDomainAdTagReportRepository implements AdNetworkDomainAdTagReportRepositoryInterface
{
    public function override(AdNetworkDomainAdTagReportInterface $report)
    {
        if ($report->getPerformanceAdNetworkDomainAdTagReport() === null && $report->getUnifiedAdNetworkDomainAdTagReport() === null) {
            return true;
        }

        $id = $this->getExistingReportId($report);

        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_ad_network_domain_ad_tag`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                `domain` = :domain,
                performance_ad_network_domain_ad_tag_report_id = :performanceAdNetworkDomainAdTagReportId,
                partner_tag_id = :partnerTagId,
                unified_ad_network_domain_ad_tag_report_id = :unifiedAdNetworkDomainAdTagReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_ad_network_domain_ad_tag`
                    (ad_network_id, date, domain, name, performance_ad_network_domain_ad_tag_report_id, partner_tag_id, unified_ad_network_domain_ad_tag_report_id)
                    VALUES (:adNetworkId, :date, :domain, :name, :performanceAdNetworkDomainAdTagReportId, :partnerTagId, :unifiedAdNetworkDomainAdTagReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('domain', $report->getDomain());
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('partnerTagId', $report->getPartnerTagId());
        $qb->bindValue('performanceAdNetworkDomainAdTagReportId', $report->getPerformanceAdNetworkDomainAdTagReport() === null ? null : $report->getPerformanceAdNetworkDomainAdTagReport()->getId());
        $qb->bindValue('unifiedAdNetworkDomainAdTagReportId', $report->getUnifiedAdNetworkDomainAdTagReport() === null ? null : $report->getUnifiedAdNetworkDomainAdTagReport()->getId());

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

    public function getExistingReportId(AdNetworkDomainAdTagReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.domain = :domain')
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.date = :date')
            ->andWhere('r.performanceAdNetworkDomainAdTagReport = :performanceReport OR r.unifiedAdNetworkDomainAdTagReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('domain', $report->getDomain())
            ->setParameter('partnerTagId', $report->getPartnerTagId())
            ->setParameter('date', $report->getDate(),Type::DATE)
            ->setParameter('performanceReport', $report->getPerformanceAdNetworkDomainAdTagReport())
            ->setParameter('unifiedReport', $report->getUnifiedAdNetworkDomainAdTagReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AdNetworkDomainAdTagReportInterface) {
            return $result->getId();
        }

        return null;
    }
}