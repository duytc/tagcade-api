<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Model\Core\ReportableAdSlotInterface;
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
    protected function getAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
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
} 