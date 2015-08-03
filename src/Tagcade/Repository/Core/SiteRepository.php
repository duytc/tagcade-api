<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class SiteRepository extends EntityRepository implements SiteRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForPublisherQuery($publisher, $limit, $offset);
        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSitesForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->addOrderBy('st.name', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('st')
            ->join('st.adSlots', 'sl')
            ->join('sl.adTags', 't')
            ->join('t.libraryAdTag', 'lt')
            ->where('lt.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER)
            ->addOrderBy('st.name', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getSitesThatHastConfigSourceReportForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.publisher = :publisher_id')
            ->leftJoin('st.sourceReportSiteConfigs', 'cf')
            ->andWhere($hasSourceReportConfig ? 'cf.site IS NOT NULL' : 'cf.site IS NULL')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.enableSourceReport = :enableSourceReport')
            ->andWhere('st.publisher = :publisher_id')
            ->setParameter('enableSourceReport', $enableSourceReport, Type::BOOLEAN)
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getAllSitesThatEnableSourceReport($enableSourceReport = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.enableSourceReport = :enableSourceReport')
            ->setParameter('enableSourceReport', $enableSourceReport, Type::BOOLEAN)
        ;

        return $qb->getQuery()->getResult();
    }



}