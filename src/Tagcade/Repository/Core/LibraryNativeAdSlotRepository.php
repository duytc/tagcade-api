<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryNativeAdSlotRepository extends EntityRepository implements LibraryNativeAdSlotRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getLibraryNativeAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->andWhere('sl.visible = true')
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

    /**
     * @inheritdoc
     */
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryNativeAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);

        return $qb->getQuery()->getResult();
    }

    public function getLibraryNativeAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryNativeAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);
        $qb->andWhere('sl.ronAdSlot IS NULL');

        return $qb->getQuery()->getResult();
    }
}