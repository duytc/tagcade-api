<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdSlotRepository extends EntityRepository implements AdSlotRepositoryInterface
{
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

    protected function getAdSlotsForSiteQuery(SiteInterface $site, $limit = null, $offset = null, $orderById = true)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->addOrderBy('sl.id', 'asc')
        ;

        if (true === $orderById) {
            $qb->addOrderBy('sl.id', 'asc');
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
    public function getAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
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
     * @param PublisherInterface $publisher
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $domain
     * @return null|BaseAdSlotInterface
     */
    public function getAdSlotForPublisherAndDomainAndLibraryAdSlot(PublisherInterface $publisher, BaseLibraryAdSlotInterface $libraryAdSlot, $domain)
    {
        $qb = $this->createQueryBuilder('sl');
        $like = $qb->expr()->like('st.domain', '?1');

        $qb->leftJoin('sl.site', 'st')
            ->where('sl.libraryAdSlot = :library_ad_slot_id')
            ->andWhere($like)
            ->andWhere('st.publisher = :publisher_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), TYPE::INTEGER)
            ->setParameter(1, '%//' . $domain . '%', TYPE::STRING)
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
            ->andWhere('sl.autoCreate = true')
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
        $qb = $this->getAdSlotsForSiteQuery($site, $limit, $offset, $orderById = false);
        $qb->andWhere(sprintf('sl INSTANCE OF %s OR sl INSTANCE OF %s', DisplayAdSlot::class, NativeAdSlot::class));

        $results = $qb->select('sl.id')->getQuery()->getArrayResult();

        return array_map(
            function($adSlotData){
                return $adSlotData['id'];
            }, $results
        );
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
}