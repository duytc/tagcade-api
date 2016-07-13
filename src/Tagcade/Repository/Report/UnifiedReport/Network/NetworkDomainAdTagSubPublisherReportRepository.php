<?php


namespace Tagcade\Repository\Report\UnifiedReport\Network;


use DateTime;
use Doctrine\DBAL\Types\Type;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\AbstractReportRepository;

class NetworkDomainAdTagSubPublisherReportRepository extends AbstractReportRepository implements NetworkDomainAdTagSubPublisherReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportFor($adNetwork, $domain, $partnerTagId, SubPublisherInterface $subPublisher, DateTime $startDate, DateTime $endDate, $oneOrNull = false)
    {
        $qb = $this->getReportsInRange($startDate, $endDate)
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.domain = :domain')
            ->andWhere('r.subPublisher = :subPublisher')
            ->setParameter('partnerTagId', $partnerTagId)
            ->setParameter('domain', $domain)
            ->setParameter('subPublisher', $subPublisher)
        ;

        if ($adNetwork instanceof AdNetworkInterface) {
            $qb
            ->andWhere('r.adNetwork = :ad_network')
            ->setParameter('ad_network', $adNetwork);
        }

        return $oneOrNull ? $qb->getQuery()->getOneOrNullResult() : $qb->getQuery()->getResult();
    }

    public function saveMultipleReport(array $reports, $override = false, $batchSize = null)
    {
        if ($override === true) {
            return $this->overrideReports($reports, $batchSize);
        }

        if ($batchSize === null) {
            $batchSize = self::BATCH_SIZE;
        }

        try {
            $count = 0;
            /** @var NetworkDomainAdTagSubPublisherReport $report */
            foreach($reports as $report) {
                $this->getEntityManager()->persist($report);
                $count++;
                if ($count % $batchSize == 0) {
                    $this->getEntityManager()->flush();
                }
            }

            // flush the remaining objects
            $this->getEntityManager()->flush();
        } catch(\Exception $ex) {
            $this->logger->error($ex->getMessage());
        }

        return true;
    }

    public function isRecordExisted(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, $domain, $partnerTagId, DateTime $date)
    {
        $result = $this
                ->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->where('r.adNetwork = :adNetwork')
                ->andWhere('r.domain = :domain')
                ->andWhere('r.partnerTagId = :partnerTagId')
                ->andWhere('r.date = :date')
                ->andWhere('r.subPublisher = :subPublisher')
                ->setParameter('adNetwork', $adNetwork)
                ->setParameter('domain', $domain)
                ->setParameter('partnerTagId', $partnerTagId)
                ->setParameter('date', $date)
                ->setParameter('subPublisher', $subPublisher)
                ->getQuery()
                ->getSingleScalarResult();

        return intval($result) > 0;
    }

    protected function overrideReports(array $reports, $batchSize = null)
    {
        if ($batchSize === null) {
            $batchSize = self::BATCH_SIZE;
        }

        $sql = 'INSERT INTO `unified_report_network_domain_ad_tag_sub_publisher`
                (sub_publisher_id, ad_network_id, domain, name, partner_tag_id, date, est_cpm, est_revenue, fill_rate, impressions, total_opportunities, passbacks)
                VALUES (:subPublisherId, :adNetworkId, :domain, :name, :partnerTagId, :date, :estCpm, :estRevenue, :fillRate, :impressions, :totalOpportunities, :passbacks)
                ON DUPLICATE KEY UPDATE
                est_revenue = :estRevenue,
                impressions = :impressions,
                total_opportunities = :totalOpportunities,
                passbacks = :passbacks,
                fill_rate = :impressions / :totalOpportunities,
                est_cpm = 1000 * :estRevenue / :impressions
                ';
        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->prepare($sql);
        $count = 0;
        $adjustedCommonReports = [];
        foreach($reports as $report) {
            if (!$report instanceof NetworkDomainAdTagSubPublisherReport) {
                continue;
            }

            $adjustedCommonReport = $this->createAdjustedCommonReport($report);
            if ($adjustedCommonReport instanceof CommonReport) {
                $adjustedCommonReports[] = $adjustedCommonReport;
            }

            $qb->bindValue('subPublisherId', $report->getSubPublisherId(), Type::INTEGER);
            $qb->bindValue('adNetworkId', $report->getAdNetworkId(), Type::INTEGER);
            $qb->bindValue('domain', $report->getDomain(), Type::INTEGER);
            $qb->bindValue('partnerTagId', $report->getPartnerTagId(), Type::STRING);
            $qb->bindValue('date', $report->getDate(), Type::DATE);
            $qb->bindValue('name', $report->getName());
            $qb->bindValue('estCpm', $report->getEstCpm() !== null ? $report->getEstCpm() : 0, Type::FLOAT);
            $qb->bindValue('estRevenue', $report->getEstRevenue() !== null ? $report->getEstRevenue() : 0, Type::FLOAT);
            $qb->bindValue('fillRate', $report->getFillRate() !== null ? $report->getFillRate() : 0, Type::FLOAT);
            $qb->bindValue('impressions', $report->getImpressions() !== null ? $report->getImpressions() : 0, Type::INTEGER);
            $qb->bindValue('totalOpportunities', $report->getTotalOpportunities() !== null ? $report->getTotalOpportunities() : 0, Type::INTEGER);
            $qb->bindValue('passbacks', $report->getPassbacks() !== null ? $report->getPassbacks() : 0, Type::INTEGER);

            // begin transaction when start loop or after reaching BATCH_SIZE
            if ($count === 0 || ($count > 0 && $count % $batchSize === 0) ) {
                $connection->beginTransaction();
            }
            try {
                if (false === $qb->execute()) {
                    throw new \Exception('Execute error');
                }
            } catch (\Exception $ex) {
                throw $ex;
            }
            $count++;
            // commit if reach BATCH_SIZE
            if ($count > 0 && $count % $batchSize === 0) {
                try {
                    $connection->commit();
                } catch (\Exception $ex) {
                    throw $ex;
                }
            }
        }

        // last commit if not reaching BATCH_SIZE again at the end
        if ($count % $batchSize !== 0) {
            try {
                $connection->commit();
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        return $adjustedCommonReports;
    }

    /**
     * Create common report that is the difference between current report and the one in database. This common report will be used to aggregate to higher
     * level reports in order to update changes
     *
     * @param NetworkDomainAdTagSubPublisherReport $report
     * @return mixed|CommonReport
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function createAdjustedCommonReport(NetworkDomainAdTagSubPublisherReport $report)
    {
        $result = $this->createQueryBuilder('r')
            ->addSelect('(:impressions - r.impressions) as impressions')
            ->addSelect('(:totalOpportunities - r.totalOpportunities) as totalOpportunities')
            ->addSelect('(:passbacks - r.passbacks) as passbacks')
            ->addSelect('(:estRevenue - r.estRevenue) as estRevenue')
            ->where('r.adNetwork = :adNetwork')
            ->andWhere('r.domain = :domain')
            ->andWhere('r.subPublisher = :subPublisher')
            ->andWhere('r.partnerTagId = :partnerTagId')
            ->andWhere('r.date = :date')
            ->setParameter('adNetwork', $report->getAdNetwork())
            ->setParameter('domain', $report->getDomain())
            ->setParameter('subPublisher', $report->getSubPublisher())
            ->setParameter('partnerTagId', $report->getPartnerTagId())
            ->setParameter('date', $report->getDate())
            ->setParameter('impressions', $report->getImpressions())
            ->setParameter('totalOpportunities', $report->getTotalOpportunities())
            ->setParameter('passbacks', $report->getPassbacks())
            ->setParameter('estRevenue', $report->getEstRevenue())
            ->getQuery()->getOneOrNullResult();

        $commonReport = new CommonReport();
        $commonReport
            ->setSubPublisher($report->getSubPublisher())
            ->setAdNetwork($report->getAdNetwork())
            ->setSite($report->getDomain())
            ->setAdTagId($report->getPartnerTagId())
            ->setDate($report->getDate())
        ;
        $configs = $report->getSubPublisher()->getSubPublisherPartnerRevenue();

        /** @var SubPublisherPartnerRevenueInterface $config */
        foreach($configs as $config) {
            if ($config->getAdNetworkPartner()->getId() !== $report->getAdNetwork()->getNetworkPartner()->getId()) {
                continue;
            }

            $commonReport->setRevenueShareConfigOption($config->getRevenueOption());
            $commonReport->setRevenueShareConfigValue($config->getRevenueValue());
        }

        // if there are records already existed, common report's values is the differences
        if ($result !== null) {
            $commonReport
                ->setImpressions(filter_var($result['impressions'], FILTER_VALIDATE_INT))
                ->setOpportunities(filter_var($result['totalOpportunities'], FILTER_VALIDATE_INT))
                ->setPassbacks(filter_var($result['passbacks'], FILTER_VALIDATE_INT))
                ->setEstRevenue(filter_var($result['estRevenue'], FILTER_VALIDATE_FLOAT))
            ;
        }
        else { // if the record is not yet existed then the common report's value is it-self
            $commonReport
                ->setImpressions($report->getImpressions())
                ->setOpportunities($report->getTotalOpportunities())
                ->setPassbacks($report->getPassbacks())
                ->setEstRevenue($report->getEstRevenue())
                ->setEstCpm($report->getEstCpm())
                ->setFillRate($report->getFillRate())
            ;
        }

        return $commonReport;
    }
}