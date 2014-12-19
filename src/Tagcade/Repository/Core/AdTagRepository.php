<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagRepository extends SortableRepository implements AdTagRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAdTagsForAdSlot(AdSlotInterface $adSlot, $limit = null, $offset = null)
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

    /**
     * @inheritdoc
     */
    public function getAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.adSlot', 'sl')
            ->leftJoin('sl.site', 'st')
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

    public function getAdTagsForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->getAdTagsForAdNetworkQuery($adNetwork)
            ->andWhere('sl.site = :site_id')
            ->join('t.adSlot', 'sl')
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
            ->andWhere('sl.site IN (:sites)')
            ->join('t.adSlot', 'sl')
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

    /**
     * @inheritdoc
     */
    public function saveAdTagPosition(AdTagInterface $adTag)
    {
        $this->getEntityManager()
            ->createQueryBuilder()
            ->update($this->getEntityName(), 't')
            ->set('t.position', ':position')
            ->where('t.id = :tag_id')
            ->setParameter('position', $adTag->getPosition(), Type::INTEGER)
            ->setParameter('tag_id', $adTag->getId(), Type::INTEGER)
            ->getQuery()
            ->execute()
        ;
    }
}