<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class AdTagRepository extends EntityRepository implements AdTagRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'rotation' => 'rotation',
        'name' => 'name',
        'frequencyCap' => 'frequencyCap',
        'impressionsCap' => 'impressionsCap',
        'networkOpportunityCap' => 'networkOpportunityCap',
        'adSlot' => 'adSlot',
        'domain' => 'domain',
        'active' => 'active'
    ];

    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.adSlot = :ad_slot_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->addOrderBy('t.position', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagIdsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id')
            ->where('t.adSlot = :ad_slot_id')
            ->andWhere('t.active = 1')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        $results = $qb->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }

    public function getAdTagsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagIdsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createAdTagForSiteQueryBuilder($site, $limit, $offset);
        $qb->andWhere('t.active = 1');

        $results = $qb->select('t.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }

    protected function createAdTagForSiteQueryBuilder(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER);

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
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->getAdTagsForPublisherQuery($publisher, $limit, $offset)->getQuery()->getResult();
    }

    public function getActiveAdTagsIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere('t.active = 1');

        $results = $qb->select('t.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }

    public function getAllActiveAdTagIds()
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.active = 1');

        $results = $qb->select('t.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }


    protected function getAdTagsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilderForPublisher($publisher)
            ->orderBy('t.id', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getAdTagsForAdNetworkQuery(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'tLib')
            ->where('tLib.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER)
            ->addOrderBy('t.position', 'asc');

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        if (is_int($limit))
            $qb->setMaxResults($limit);

        return $qb;
    }

    public function getAdTagIdsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork, $limit, $offset)->andWhere('t.active = 1');
        $results = $qb->select('t.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }

    public function getAdTagsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    protected function getAdTagsForAdNetworkQueryBuilder(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'tLib')
            ->where('tLib.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER);;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork);

        $qb->leftJoin('t.adSlot', 'sl')
            ->leftJoin('sl.libraryAdSlot', 'lsl')
            ->leftJoin('sl.site', 'st');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('tLib.name', ':searchKey'),
                $qb->expr()->like('t.id', ':searchKey'),
                $qb->expr()->like('lsl.name', ':searchKey'),
                $qb->expr()->like('st.name', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['active']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['id']:
                case $this->SORT_FIELDS['rotation']:
                case $this->SORT_FIELDS['frequencyCap']:
                case $this->SORT_FIELDS['impressionsCap']:
                case $this->SORT_FIELDS['networkOpportunityCap']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('tLib.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['adSlot']:
                    $qb->addOrderBy('lsl.name', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['domain']:
                    $qb->addOrderBy('st.name', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    public function getAdTagsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.adSlot', 'sl')
            ->leftJoin('t.libraryAdTag', 'lat')
            ->leftJoin('sl.libraryAdSlot', 'lsl')
            ->leftJoin('sl.site', 'st')
        ;

        if ($user instanceof PublisherInterface) {
            $qb = $this->createQueryBuilderForPublisher($user)
                ->leftJoin('sl.libraryAdSlot', 'lsl')
                ->leftJoin('t.libraryAdTag', 'lat')
            ;
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('lat.name', ':searchKey'),
                    $qb->expr()->like('t.id', ':searchKey'),
                    $qb->expr()->like('lsl.name', ':searchKey'),
                    $qb->expr()->like('st.name', ':searchKey'),
                    $qb->expr()->like('st.domain', ':searchKey')
                ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case $this->SORT_FIELDS['active']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['rotation']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['frequencyCap']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['impressionsCap']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['networkOpportunityCap']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['domain']:
                    $qb->addOrderBy('st.' . 'name', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    public function getAdTagsForSiteWithPagination(SiteInterface $site, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.adSlot', 'sl')
            ->leftJoin('sl.libraryAdSlot', 'lsl')
            ->leftJoin('t.libraryAdTag', 'tLib')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('tLib.name', ':searchKey'),
                $qb->expr()->like('t.id', ':searchKey'),
                $qb->expr()->like('lsl.name', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['active']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['id']:
                case $this->SORT_FIELDS['rotation']:
                case $this->SORT_FIELDS['frequencyCap']:
                case $this->SORT_FIELDS['impressionsCap']:
                case $this->SORT_FIELDS['networkOpportunityCap']:
                    $qb->addOrderBy('t.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('tLib.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['adSlot']:
                    $qb->addOrderBy('lsl.name', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }


    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->andWhere('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER);

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
    public function getAdTagsForAdNetworkAndSiteWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->andWhere('sl.site = :site_id')
            ->andWhere('st.subPublisher = :sub_publisher_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->setParameter('sub_publisher_id', $subPublisher->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->andWhere('sl.site IN (:sites)')
            ->setParameter('sites', $sites);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->andWhere('sl.site = :site_id')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }


    public function getAdTagsByAdSlotAndLibraryAdTag(BaseAdSlotInterface $adSlot, LibraryAdTagInterface $libraryAdTag, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.adSlot = :ad_slot_id')
            ->andWhere('t.adTagLibrary = :ad_tag_library_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->setParameter('ad_tag_library_id', $libraryAdTag->getId(), Type::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return AdTagInterface[]
     */
    public function getSharedAdTagsForAdSlot(BaseAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adTagLibrary', 'tl')
            ->where('tl.visible = true')
            ->andWhere('t.adSlot = :ad_slot_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER);

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsByAdSlotAndRefId(BaseAdSlotInterface $adSlot, $refId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.adSlot = :ad_slot_id')
            ->andWhere('t.refId = :ref_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING);

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->where('sl.libraryAdSlot = :library_ad_slot_id')
            ->andWhere('t.refId = :ref_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING);

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
    public function getAdTagsByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->where('sl.libraryAdSlot = :library_ad_slot_id')
            ->andWhere('t.refId != :ref_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getActiveAdTagIdsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork, $limit, $offset)
            ->join('t.adSlot', 'sl')
            ->andWhere('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->andWhere('t.active = 1');

        $results = $qb->select('t.id')->getQuery()->getArrayResult();

        return array_map(function ($resultItem) {
                return $resultItem['id'];
            }, $results);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPartner(AdNetworkPartnerInterface $partner, UserRoleInterface $user, $partnerTagId = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'tLib')
            ->join('tLib.adNetwork', 'nw')
            ->where('nw.networkPartner = :partner_id')
            ->setParameter('partner_id', $partner->getId(), Type::INTEGER);

        if ($user instanceof PublisherInterface) {
            $qb->andWhere('nw.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), Type::INTEGER);
        }

        if ($partnerTagId != null) {
            $qb->andWhere('tLib.partnerTagId = :partner_tag_id')
                ->setParameter('partner_tag_id', $partnerTagId, Type::STRING);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAllAdTagsByStatus($status)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.active = :status')
            ->setParameter('status', $status, Type::INTEGER);

        return $qb->getQuery()->getResult();
    }

    /**
     * create QueryBuilder For Publisher due to Publisher or SubPublisher
     * @param PublisherInterface $publisher
     * @return QueryBuilder qb with alias 't'
     */
    private function createQueryBuilderForPublisher(PublisherInterface $publisher)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st');

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
     * Get all adTag that set impression cap and by status
     * @param $status
     * @return array
     */
    public function getAdTagsThatSetImpressionAndOpportunityCapByStatus ($status)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.impressionCap IS NOT NULL OR t.networkOpportunityCap IS NOT NULL')
            ->andWhere('t.active = :status')
            ->setParameter('status', $status, Type::INTEGER);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param LibraryAdTag $libraryAdTag
     * @return array
     */
    public function getAdTagsHaveTheSameAdTabLib(LibraryAdTag $libraryAdTag)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.libraryAdTag =:libraryAdTag')
            ->setParameter('libraryAdTag', $libraryAdTag);

        return $qb->getQuery()->getResult();
    }

    public function isSiteActiveForAdNetwork(AdNetworkInterface $adNetwork, SiteInterface $site)
    {
        return $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'lib')
            ->join('t.adSlot', 'slot')
            ->select('count(t.id)')
            ->where('lib.adNetwork = :network')
            ->setParameter('network', $adNetwork)
            ->andWhere('slot.site = :site')
            ->setParameter('site', $site)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function getActiveSitesForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork, PublisherInterface $publisher = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'lib')
            ->join('t.adSlot', 'slot')
            ->join('slot.site', 'site')
            ->select('site.id')
            ->distinct()
            ->where('lib.adNetwork = :network')
            ->setParameter('network', $adNetwork);

        if ($publisher instanceof PublisherInterface) {
            $qb->andWhere('site.publisher = :publisher')
                ->setParameter('publisher', $publisher);
        }

        return $qb->getQuery()->getScalarResult();
    }

    public function countAdTagForSiteAndAdNetworkByStatus(AdNetworkInterface $adNetwork, SiteInterface $site, $status)
    {
        return $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'lib')
            ->join('t.adSlot', 'slot')
            ->select('count(t.id)')
            ->where('lib.adNetwork = :network')
            ->setParameter('network', $adNetwork)
            ->andWhere('slot.site = :site')
            ->setParameter('site', $site)
            ->andWhere('t.active = :status')
            ->setParameter('status', $status)
            ->getQuery()->getSingleScalarResult();
    }
}