<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdSlotRepository extends EntityRepository implements AdSlotRepositoryInterface
{
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
        $qb = $this->createQueryBuilder('sl')
            ->where(sprintf('sl INSTANCE OF %s', NativeAdSlot::class))
            ->orWhere(sprintf('sl INSTANCE OF %s', DisplayAdSlot::class))
        ;

        return $qb->getQuery()->getResult();
    }

    protected function getAdSlotsForSiteQuery(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->addOrderBy('sl.id', 'asc')
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
}