<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
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
            ->addOrderBy('st.name', 'asc');

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
        return $this->getSitesThatHaveAdTagsBelongingToAdNetworkQuery($adNetwork, $limit, $offset)->getQuery()->getResult();
    }

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToAdNetworkQuery($adNetwork, $limit, $offset);
        $results = $qb->select('st.id')->getQuery()->getArrayResult();

        return array_map(function($resultItem) { return $resultItem['id']; }, $results);
    }


    protected function getSitesThatHaveAdTagsBelongingToAdNetworkQuery(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('st')
            ->join('st.adSlots', 'sl')
            ->join('sl.adTags', 't')
            ->join('t.libraryAdTag', 'lt')
            ->where('lt.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER)
            ->addOrderBy('st.name', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getSitesThatHastConfigSourceReportForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.publisher = :publisher_id')
            ->leftJoin('st.sourceReportSiteConfigs', 'cf')
            ->andWhere($hasSourceReportConfig ? 'cf.site IS NOT NULL' : 'cf.site IS NULL')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        return $qb->getQuery()->getResult();
    }

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.enableSourceReport = :enableSourceReport')
            ->andWhere('st.publisher = :publisher_id')
            ->setParameter('enableSourceReport', $enableSourceReport, Type::BOOLEAN)
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);

        return $qb->getQuery()->getResult();
    }

    public function getAllSitesThatEnableSourceReport($enableSourceReport = true)
    {
        $qb = $this->createQueryBuilder('st')
            ->where('st.enableSourceReport = :enableSourceReport')
            ->setParameter('enableSourceReport', $enableSourceReport, Type::BOOLEAN);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null)
    {
        $referencedSites = array_map(
            function (BaseAdSlotInterface $adSlot) {
                return $adSlot->getSite();
            },
            $slotLibrary->getAdSlots()->toArray()
        );
        $referencedSites = array_unique($referencedSites);

        $qb = $this->createQueryBuilder('st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $slotLibrary->getPublisherId());

        if (count($referencedSites) > 0) {
            $qb->andWhere('st NOT IN (:referencedSites)')
                ->setParameter('referencedSites', $referencedSites);
        }

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Check if a site with the given domain already existed
     *
     * @param $domain
     * @param PublisherInterface $publisher
     * @return bool
     */
    public function getSiteByDomainAndPublisher(PublisherInterface $publisher, $domain)
    {
        if (!is_string($domain)) {
            throw new InvalidArgumentException('expect an object of string');
        }

        $qb = $this->createQueryBuilder('s');
        return $qb->where('s.domain = :domain')
            ->andWhere('s.publisher = :publisher_id')
            ->setParameter('domain', $domain, TYPE::STRING)
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
            ->getQuery()->getOneOrNullResult();
    }
}