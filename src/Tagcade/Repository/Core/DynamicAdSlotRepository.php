<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DynamicAdSlotRepository extends EntityRepository implements DynamicAdSlotRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
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

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getDynamicAdSlotsForPublisherQuery($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
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