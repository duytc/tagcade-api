<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagRepository extends EntityRepository implements AdTagRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.adSlot = :ad_slot_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->addOrderBy('t.position', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->where('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;

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
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->orderBy('t.id', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForAdNetworkQuery(AdNetworkInterface $adNetwork)
    {
        return $this->createQueryBuilder('t')
            ->join('t.libraryAdTag', 'tLib')
            ->where('tLib.adNetwork = :ad_network_id')
            ->setParameter('ad_network_id', $adNetwork->getId(), Type::INTEGER)
            ->addOrderBy('t.position', 'asc')
        ;
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

    public function getAdTagsForAdNetworkFilterPublisher(AdNetworkInterface $adNetwork,$limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->join('sl.site', 'st')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER);
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('t.adSlot', 'sl')
            ->andWhere('sl.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;

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
            ->setParameter('sites', $sites)
        ;

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
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER)
        ;

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
            ->setParameter('ad_tag_library_id', $libraryAdTag->getId(), Type::INTEGER)
        ;

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
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getAdTagsByAdSlotAndRefId(BaseAdSlotInterface $adSlot, $refId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.adSlot = :ad_slot_id')
            ->andWhere('t.refId = :ref_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING)
        ;

        return $qb->getQuery()->getResult();
    }
}