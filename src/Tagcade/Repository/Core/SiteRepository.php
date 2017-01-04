<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Behaviors\CreateSiteTokenTrait;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class SiteRepository extends EntityRepository implements SiteRepositoryInterface
{
    use CreateSiteTokenTrait;

    protected $SORT_FIELDS = ['id', 'name', 'domain', 'rtbStatus'];

    /**
     * @inheritdoc
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForPublisherQuery($publisher, $limit, $offset);
        return $qb->getQuery()->getResult();
    }

    public function getSitesForPublishers(array $publishers, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('st')
            ->join('st.publisher', 'p');

        $qb->where($qb->expr()->in('p.id', $publishers))
            ->addOrderBy('st.name', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getRTBEnabledSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere('st.rtbEnabled = true');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAutoCreatedSites(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForUserQuery($user, $limit, $offset);
        $qb->andWhere('st.autoCreate = :autoCreate')
            ->setParameter('autoCreate', true, Type::BOOLEAN);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getManualCreatedSites(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForUserQuery($user, $limit, $offset);
        $qb->andWhere('st.autoCreate = :autoCreate')
            ->setParameter('autoCreate', false, Type::BOOLEAN);

        return $qb->getQuery()->getResult();
    }

    public function getSitesForUserQuery(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getSitesForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilderForPublisher($publisher)
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

    /**
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToPartnerQuery($partnerId = 'all', $limit, $offset);

        if ($publisher instanceof SubPublisherInterface) {
            $qb->andWhere('st.subPublisher = :subPublisherId')
                ->setParameter('subPublisherId', $publisher->getId());
        } else {
            $qb->andWhere('st.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisher->getId());
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
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartner(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToPartnerQuery($adNetwork->getId(), $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToPartnerQuery($adNetwork->getId(), $limit, $offset);

        $qb
            ->andWhere('st.subPublisher = :subPublisher')
            ->setParameter('subPublisher', $subPublisher);

        return $qb->getQuery()->getResult();
    }

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToAdNetworkQuery($adNetwork, $limit, $offset);
        $results = $qb->select('st.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
            return $resultItem['id'];
        }, $results);
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

    protected function getSitesThatHaveAdTagsBelongingToPartnerQuery($adNetworkId = 'all', $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('st')
            ->join('st.adSlots', 'sl')
            ->join('sl.adTags', 't')
            ->join('t.libraryAdTag', 'lt');

        if ($adNetworkId != 'all') {
            $qb->where('lt.adNetwork = :network_id')
                ->setParameter('network_id', $adNetworkId, Type::INTEGER);
        } else {
            $qb
                ->join('lt.adNetwork', 'nw')
                ->where($qb->expr()->isNotNull('nw.networkPartner'));
        }

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
     * @inheritdoc
     */
    public function getSitesByDomainAndPublisher(PublisherInterface $publisher, $domain, $useHash = false)
    {
        if (!is_string($domain)) {
            throw new InvalidArgumentException('expect an object of string');
        }

        $qb = $this->createQueryBuilder('s');
        if (true === $useHash) {
            $hash = $this->createSiteHash($publisher->getId(), $domain);
            return $qb->where('s.siteToken = :hash')->setParameter('hash', $hash)->getQuery()->getOneOrNullResult();
        }

        return $qb->where('s.domain = :domain')
            ->andWhere('s.publisher = :publisher_id')
            ->setParameter('domain', $domain, TYPE::STRING)
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER)
            ->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSitesByDomain($domain)
    {
        if (!is_string($domain)) {
            throw new InvalidArgumentException('expect an object of string');
        }

        $qb = $this->createQueryBuilder('s')
            ->where('s.domain = :domain')
            ->setParameter('domain', $domain, TYPE::STRING);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getSitesNotBelongToSubPublisherForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getSitesForPublisherQuery($publisher, $limit, $offset);

        $qb
            ->andWhere('st.subPublisher IS NULL');

        return $qb->getQuery()->getResult();
    }

    /**
     * create QueryBuilder For Publisher due to Publisher or SubPublisher
     * @param PublisherInterface $publisher
     * @return QueryBuilder qb with alias 'st'
     */
    private function createQueryBuilderForPublisher(PublisherInterface $publisher)
    {
        $qb = $this->createQueryBuilder('st');

        if ($publisher instanceof SubPublisherInterface) {
            $qb
                ->where('st.subPublisher = :sub_publisher_id')
                ->setParameter('sub_publisher_id', $publisher->getId(), Type::INTEGER);
        } else {
            $qb
                ->where('st.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);
        }

        return $qb;
    }

    /**
     * @param PublisherInterface $publisher
     * @return array
     */
    public function getUniqueDomainsForPublisher(PublisherInterface $publisher)
    {
        return $this->createQueryBuilderForPublisher($publisher)
            ->select('st.domain as domain')
            ->distinct()
            ->getQuery()->getResult();
    }

    /**
     * create QueryBuilder For User due to Admin or Publisher|SubPublisher
     * @param UserRoleInterface $user
     * @return QueryBuilder qb with alias 'st'
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof PublisherInterface ? $this->createQueryBuilderForPublisher($user) : $this->createQueryBuilder('st');
    }

    public function getSitesForUserWithPagination(UserRoleInterface $user, PagerParam $param, $autoCreate = null, $enableSourceReport = null)
    {
        $qb = $this->createQueryBuilderForUser($user);

        if (is_int($autoCreate)) {
            $qb->andWhere('st.autoCreate = :autoCreate')
                ->setParameter('autoCreate', $autoCreate);
        }

        if (is_bool($enableSourceReport)) {
            $qb->andWhere('st.enableSourceReport = :enableSourceReport')
                ->setParameter('enableSourceReport', $enableSourceReport);
        }

        if (is_int($param->getPublisherId()) && $param->getPublisherId() > 0) {
            $qb->andWhere('st.publisher = :publisherId')
                ->setParameter('publisherId', $param->getPublisherId());
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('st.name', ':searchKey'), $qb->expr()->like('st.domain', ':searchKey')))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('st.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    public function getSiteHavingAdTagBelongsToAdNetworkFilterByPublisher(AdNetworkInterface $adNetwork, $publisher = null)
    {
        $qb = $this->getSitesThatHaveAdTagsBelongingToAdNetworkQuery($adNetwork);
        if ($publisher instanceof PublisherInterface) {
            if ($publisher instanceof SubPublisherInterface) {
                $qb->andWhere('st.subPublisher = :publisher');
            } else {
                $qb->andWhere('st.publisher = :publisher');
            }
            $qb->setParameter('publisher', $publisher);
        }

        return $qb->getQuery()->getResult();
    }

    public function findSubPublisherByDomainFilterPublisher(PublisherInterface $publisher, $domain)
    {
        return $this->createQueryBuilder('s')
            ->join('s.subPublisher', 'sub')
            ->select('sub.id')
            ->distinct()
            ->where('s.domain = :domain')
            ->andWhere('sub.publisher = :publisher')
            ->setParameter('domain', $domain)
            ->setParameter('publisher', $publisher)
            ->getQuery()->getScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function getSiteBySiteToken($siteToken)
    {
        if (!is_string($siteToken)) {
            throw new InvalidArgumentException('expect an object of string');
        }

        $qb = $this->createQueryBuilder('s')
            ->where('s.siteToken = :siteToken')
            ->setParameter('siteToken', $siteToken, TYPE::STRING);

        return $qb->getQuery()->getResult();
    }


    public function getSiteByPublisherAndSiteName(PublisherInterface $publisher, $siteName)
    {
        $qb = $this->createQueryBuilderForPublisher($publisher);
        $qb->andWhere('st.name = :siteName')
           ->setParameter('siteName', $siteName);

        return $qb->getQuery()->getResult();
    }

}