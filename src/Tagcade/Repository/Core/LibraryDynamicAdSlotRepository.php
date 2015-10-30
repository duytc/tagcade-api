<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class LibraryDynamicAdSlotRepository extends EntityRepository implements LibraryDynamicAdSlotRepositoryInterface
{
    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryDynamicAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);

        return $qb->getQuery()->getResult();
    }

    public function getLibraryDynamicAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryDynamicAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);
        $qb->andWhere('sl.ronAdSlot IS NULL');

        return $qb->getQuery()->getResult();
    }


    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getLibraryDynamicAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
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
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByDefaultLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->where('sl.defaultLibraryAdSlot = :default_library_ad_slot_id')
            ->setParameter('default_library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }


}