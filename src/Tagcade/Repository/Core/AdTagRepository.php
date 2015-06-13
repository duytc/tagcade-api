<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Behavior\ArrayTrait;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagRepository extends EntityRepository implements AdTagRepositoryInterface
{
    use ArrayTrait;
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
            ->leftJoin('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->where('dAdSlot.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;
        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->where('nAdSlot.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;
        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        $allAdTags = array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot);

        return $this->sliceArray(array_unique($allAdTags), $offset, $limit);
    }

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->leftJoin('dAdSlot.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->orderBy('t.id', 'asc')
        ;
        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->leftJoin('nAdSlot.site', 'st')
            ->where('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->orderBy('t.id', 'asc')
        ;
        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        $allAdTags = array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot);

        return $this->sliceArray(array_unique($allAdTags), $offset, $limit);
    }

    public function getAdTagsForAdNetworkQuery(AdNetworkInterface $adNetwork)
    {
        return $this->createQueryBuilder('t')
            ->where('t.adNetwork = :ad_network_id')
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
            ->join('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->join('dAdSlot.site', 'st')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER);
        ;

        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->join('nAdSlot.site', 'st')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER);
        ;

        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        return $this->sliceArray(array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot), $offset, $limit);
    }

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->andWhere('dAdSlot.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;

        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->andWhere('nAdSlot.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
        ;

        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        $allFoundAdTags = array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot);

        return $this->sliceArray($allFoundAdTags, $offset, $limit);
    }

    public function getAdTagsForAdNetworkAndSites(AdNetworkInterface $adNetwork, array $sites, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->andWhere('dAdSlot.site IN (:sites)')
            ->setParameter('sites', $sites)
        ;

        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->andWhere('nAdSlot.site IN (:sites)')
            ->setParameter('sites', $sites)
        ;

        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        $allFoundAdTags = array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot);

        return $this->sliceArray($allFoundAdTags, $offset, $limit);
    }

    public function getAdTagsForAdNetworkAndSiteFilterPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\AdSlot', 'dAdSlot', 'WITH', 't.adSlot = dAdSlot.id')
            ->join('dAdSlot.site', 'st')
            ->andWhere('dAdSlot.site = :site_id')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER)
        ;

        $adTagsForDisplayAdSlot = $qb->getQuery()->getResult();

        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->join('Tagcade\\Entity\Core\\NativeAdSlot', 'nAdSlot', 'WITH', 't.adSlot = nAdSlot.id')
            ->join('nAdSlot.site', 'st')
            ->andWhere('nAdSlot.site = :site_id')
            ->andwhere('st.publisher = :publisher_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->setParameter('publisher_id', $adNetwork->getPublisherId(), Type::INTEGER)
        ;

        $adTagsForNativeAdSlot = $qb->getQuery()->getResult();

        return $this->sliceArray(array_merge($adTagsForDisplayAdSlot, $adTagsForNativeAdSlot), $offset, $limit);
    }
}