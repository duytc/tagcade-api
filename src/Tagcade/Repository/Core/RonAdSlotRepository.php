<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class RonAdSlotRepository extends EntityRepository implements RonAdSlotRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function getRonAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('ra')
        ->join('ra.libraryAdSlot', 'la')
        ->where('la.publisher = :publisher_id')
        ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param SegmentInterface $segment
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getRonAdSlotsForSegment(SegmentInterface $segment, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('rsl')
            ->leftJoin('rsl.ronAdSlotSegments', 'rsls')
            ->where('rsls.segment = :segment_id')
            ->setParameter('segment_id', $segment->getId(), TYPE::INTEGER);

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
    public function getRonDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('ra')
            ->join('ra.libraryAdSlot', 'la')
            ->join('Tagcade\Entity\Core\LibraryDisplayAdSlot', 'lda', Join::WITH, 'lda.id = la.id')
            ->where('la.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), TYPE::INTEGER);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        $a = $qb->getQuery()->getResult();
        return $a;
    }
}