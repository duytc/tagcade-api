<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdSlotRepository extends EntityRepository implements AdSlotRepositoryInterface
{

    protected $SORT_FIELDS = ['id'=>'id','name'=> 'name', 'channel'=>'channel',
                              'domain'=>'domain', 'size'=>'size','type'=>'type', 'rtb'=>'rtb'];

    public function allReportableAdSlotIds()
    {
        $results = $this->getAllReportableAdSlotsQuery()->select('sl.id')->getQuery()->getResult();

        return array_map(
            function($adSlotData){
                return $adSlotData['id'];
            }, $results
        );
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    public function getDisplayAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', DisplayAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getNativeAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', NativeAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', DynamicAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    public function getDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', DisplayAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', NativeAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s', DynamicAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getReportableAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        return $qb->getQuery()->getResult();
    }

    public function allReportableAdSlots($limit = null, $offset = null)
    {
        return $this->getAllReportableAdSlotsQuery($limit, $offset)->getQuery()->getResult();
    }

    protected function getAllReportableAdSlotsQuery($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where(sprintf('sl INSTANCE OF %s', NativeAdSlot::class))
            ->orWhere(sprintf('sl INSTANCE OF %s', DisplayAdSlot::class))
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    protected function getAdSlotsForSiteQuery(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;

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
    public function getAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilderForPublisher($publisher)
            ->orderBy('sl.id', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    public function getReferencedAdSlotsForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $libraryAdSlot, $offset)
            ->andWhere('sl.libraryAdSlot = :library_ad_slot_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId())
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getCoReferencedAdSlots(BaseLibraryAdSlotInterface $libraryAdSlot)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.libraryAdSlot = :library_ad_slot_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId())
            ->addOrderBy('sl.id', 'asc')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * There is only one ad slot created from library ad slot and domain
     *
     * @param PublisherInterface $publisher
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $domain
     * @return null|BaseAdSlotInterface
     */
    public function getAdSlotForPublisherAndDomainAndLibraryAdSlot(PublisherInterface $publisher, BaseLibraryAdSlotInterface $libraryAdSlot, $domain)
    {
        $qb = $this->createQueryBuilder('sl');
        $qb->leftJoin('sl.site', 'st')
            ->where('sl.libraryAdSlot = :library_ad_slot_id')
            ->andWhere('st.domain = :domain')
            ->andWhere('st.publisher = :publisher_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), TYPE::INTEGER)
            ->setParameter('domain', $domain, TYPE::STRING)
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get all AdSlot that was created from the given RonAdSlot
     *
     * @param RonAdSlotInterface $ronAdSlot
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getByRonAdSlot(RonAdSlotInterface $ronAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.libraryAdSlot', 'lsl')
            ->join('lsl.ronAdSlot', 'rsl')
            ->where('rsl.id = :ron_ad_slot_id')
            /*->andWhere('sl.autoCreate = true') -- don not check autoCreate or not, we get all ad slot images for ron ad slot */
            ->setParameter('ron_ad_slot_id', $ronAdSlot->getId(), TYPE::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getReportableAdSlotIdsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        $results = $qb->select('sl.id')->getQuery()->getArrayResult();

        return array_map(
            function($adSlotData){
                return $adSlotData['id'];
            }, $results
        );
    }

    public function getReportableAdSlotForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset);
        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        return $qb->getQuery()->getResult();
    }


    public function getReportableAdSlotIdsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdSlotsForPublisherQuery($publisher, $limit, $offset, $orderById = false);
        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        $results = $qb->select('sl.id')->getQuery()->getArrayResult();

        return array_map(
            function($adSlotData){
                return $adSlotData['id'];
            }, $results
        );
    }

    public function getReportableAdSlotIdsRelatedAdNetwork(AdNetworkInterface $adNetwork)
    {
        $qb = $this->createQueryBuilder('sl')
            ->join('sl.adTags', 't')
            ->join('t.libraryAdTag', 'lt')
            ->where('lt.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER);

        $results = $qb->select('sl.id')->getQuery()->getArrayResult();

        return array_map(
            function($adSlotData){
                return $adSlotData['id'];
            }, $results
        );
    }

    /**
     * create QueryBuilder For Publisher due to Publisher or SubPublisher
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder qb with alias 'sl'
     */
    protected function createQueryBuilderForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st');

        if ($publisher instanceof SubPublisherInterface) {
            $qb
                ->where('st.subPublisher = :sub_publisher_id')
                ->setParameter('sub_publisher_id', $publisher->getId(), Type::INTEGER);
        } else {
            $qb
                ->where('st.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);
        }

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
    public function getAdSlotsRelatedChannelForUser(UserRoleInterface $user, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st')
            ->orderBy('sl.id', 'asc');

        if ($user instanceof PublisherInterface) {
            // override prev $qb
            $qb = $this->getAdSlotsForPublisherQuery($user);
        }

        $qb->join('st.channelSites', 'cs');

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
    public function getAdSlotsForChannel(ChannelInterface $channel, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st')
            ->join('st.channelSites', 'cs')
            ->join('cs.channel', 'cn')
            ->where('cn.id = :channelId')
            ->setParameter('channelId', $channel->getId())
            ->orderBy('sl.id', 'asc');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @return QueryBuilder
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof PublisherInterface ? $this->createQueryBuilderForPublisher($user) : $this->createQueryBuilder('sl');
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param =null)
    {
        $qb = $this->createQueryBuilderForUser($user);
        if ($user instanceof AdminInterface) {
            $qb->join('sl.site', 'st');
        }

        $qb->join('sl.libraryAdSlot', 'lsl');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('lsl.name', ':searchKey'),
                    $qb->expr()->like('sl.id', ':searchKey'),
                    $qb->expr()->like('st.name', ':searchKey'), $qb->expr()->like('st.domain', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['domain']:
                    $qb->addOrderBy('st.' . 'name', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['rtb']:
                    $qb->addOrderBy('st.' . 'rtbStatus', $param->getSortDirection());
                    break;
                default:
                    break;
                    }
            }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getRelatedChannelWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('sl')
            ->join('sl.site', 'st');

        if ($user instanceof PublisherInterface) {
            // override prev $qb
            $qb = $this->getAdSlotsForPublisherQuery($user);
        }

        $qb->join('sl.libraryAdSlot', 'lsl');

        $qb->join('st.channelSites', 'cs');

        $qb->join('cs.channel', 'cn');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('lsl.name', ':searchKey'), $qb->expr()->like('cn.name', ':searchKey')))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['channel']:
                    $qb->addOrderBy('cn.' . 'name', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }
        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getReportableAdSlotQuery(PublisherInterface $publisher, PagerParam $param, $limit = null, $offset = null )
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st')
            ->leftJoin('sl.libraryAdSlot','lbs');

        if ($publisher instanceof SubPublisherInterface) {
            $qb
                ->where('st.subPublisher = :sub_publisher_id')
                ->setParameter('sub_publisher_id', $publisher->getId(), Type::INTEGER);
        } else {
            $qb
                ->where('st.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);
        }

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('lbs.name', ':searchKey'), $qb->expr()->like('st.name', ':searchKey')))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()){
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('sl.libraryAdSlot' . $param->getSortField(), $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }
}