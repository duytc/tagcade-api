<?php


namespace Tagcade\Repository\Report\UnifiedReport\Comparison;

use Doctrine\DBAL\Types\Type;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepository as UnifiedAdNetworkDomainAdTagSubPublisherReportRepository;

class AdNetworkDomainAdTagSubPublisherReportRepository extends UnifiedAdNetworkDomainAdTagSubPublisherReportRepository implements AdNetworkDomainAdTagSubPublisherReportRepositoryInterface
{
    public function override(AdNetworkDomainAdTagSubPublisherReportInterface $report)
    {
        if ($report->getPerformanceAdNetworkDomainAdTagSubPublisherReport() === null && $report->getUnifiedAdNetworkDomainAdTagSubPublisherReport() === null) {
            throw new RuntimeException('both Performance and Unified Report can not be null');
        }

        $id = $this->getExistingReportId($report);
        if (is_int($id)) {
            $sql = 'UPDATE `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher`
                SET ad_network_id = :adNetworkId,
                `date` = :date,
                `name` = :name,
                `domain` = :domain,
                performance_ad_network_domain_ad_tag_sub_publisher_report_id = :performanceAdNetworkDomainAdTagSubPublisherReportId,
                partner_tag_id = :partnerTagId,
                sub_publisher_id = :subPublisherId,
                unified_ad_network_domain_ad_tag_sub_publisher_report_id = :unifiedAdNetworkDomainAdTagSubPublisherReportId
                WHERE id = :id
            ';
        } else {
            $sql = 'INSERT INTO `unified_report_comparison_ad_network_domain_ad_tag_sub_publisher`
                    (ad_network_id, date, domain, name, performance_ad_network_domain_ad_tag_sub_publisher_report_id, partner_tag_id, sub_publisher_id, unified_ad_network_domain_ad_tag_sub_publisher_report_id)
                    VALUES (:adNetworkId, :date, :domain, :name, :performanceAdNetworkDomainAdTagSubPublisherReportId, :partnerTagId, :subPublisherId, :unifiedAdNetworkDomainAdTagSubPublisherReportId)
                    ';
        }

        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);

        $qb->bindValue('adNetworkId', $report->getAdNetwork()->getId(), Type::INTEGER);
        $qb->bindValue('date', $report->getDate(), Type::DATE);
        $qb->bindValue('domain', $report->getDomain());
        $qb->bindValue('name', $report->getName());
        $qb->bindValue('subPublisherId', $report->getSubPublisherId());
        $qb->bindValue('partnerTagId', $report->getPartnerTagId());
        $qb->bindValue('performanceAdNetworkDomainAdTagSubPublisherReportId', $report->getPerformanceAdNetworkDomainAdTagSubPublisherReport() === null ? null : $report->getPerformanceAdNetworkDomainAdTagSubPublisherReport()->getId());
        $qb->bindValue('unifiedAdNetworkDomainAdTagSubPublisherReportId', $report->getUnifiedAdNetworkDomainAdTagSubPublisherReport() === null ? null : $report->getUnifiedAdNetworkDomainAdTagSubPublisherReport()->getId());

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

    public function getExistingReportId(AdNetworkDomainAdTagSubPublisherReportInterface $report)
    {
        $result = $this->createQueryBuilder('r')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.date = :date')
            ->andWhere('r.domain = :domain')
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.subPublisher = :subPublisher')
            ->andWhere('r.performanceAdNetworkDomainAdTagSubPublisherReport = :performanceReport OR r.unifiedAdNetworkDomainAdTagSubPublisherReport = :unifiedReport')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('date', $report->getDate(), Type::DATE)
            ->setParameter('domain', $report->getDomain())
            ->setParameter('partnerTagId', $report->getPartnerTagId())
            ->setParameter('subPublisher', $report->getSubPublisher())
            ->setParameter('performanceReport', $report->getPerformanceAdNetworkDomainAdTagSubPublisherReport())
            ->setParameter('unifiedReport', $report->getUnifiedAdNetworkDomainAdTagSubPublisherReport())
            ->getQuery()
            ->getOneOrNullResult();

        if ($result instanceof AdNetworkDomainAdTagSubPublisherReportInterface) {
            return $result->getId();
        }

        return null;
    }
}