<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Tagcade\Model\Core\AdSlotInterface;
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
}