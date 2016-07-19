<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepository as UnifiedNetworkDomainReportRepository;

class AdNetworkDomainReportRepository extends UnifiedNetworkDomainReportRepository implements AdNetworkDomainReportRepositoryInterface
{
    public function override(AdNetworkDomainReportInterface $report)
    {
        if ($report->getPerformanceAdNetworkDomainReport() === null && $report->getUnifiedNetworkSiteReport() === null) {
            return true;
        }

        $id = $this->getExistingReportId($report);

        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_ad_network_domain`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                `domain` = :domain,
                performance_ad_network_domain_report_id = :performanceAdNetworkDomainReportId,
                unified_network_site_report_id = :unifiedNetworkSiteReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_ad_network_domain`
                    (ad_network_id, date, domain, name, performance_ad_network_domain_report_id, unified_network_site_report_id)
                    VALUES (:adNetworkId, :date, :domain, :name, :performanceAdNetworkDomainReportId, :unifiedNetworkSiteReportId)
                    ';
        }


        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('domain', $report->getDomain());
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('performanceAdNetworkDomainReportId', $report->getPerformanceAdNetworkDomainReport() === null ? null : $report->getPerformanceAdNetworkDomainReport()->getId());
        $qb->bindValue('unifiedNetworkSiteReportId', $report->getUnifiedNetworkSiteReport() === null ? null : $report->getUnifiedNetworkSiteReport()->getId());

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

    public function getExistingReportId(AdNetworkDomainReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.date = :date')
            ->andWhere('r.domain = :domain')
            ->andWhere('r.performanceAdNetworkDomainReport = :performanceReport OR r.unifiedNetworkSiteReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('domain', $report->getDomain())
            ->setParameter('performanceReport', $report->getPerformanceAdNetworkDomainReport())
            ->setParameter('unifiedReport', $report->getUnifiedNetworkSiteReport())
            ->getQuery()
            ->getOneOrNullResult();
        if ($result instanceof AdNetworkDomainReportInterface) {
            return $result->getId();
        }

        return null;
    }
}