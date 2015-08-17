<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class LibraryExpressionRepository extends EntityRepository implements LibraryExpressionRepositoryInterface
{
    /**
     * Get all library expressions that have expect library ad slot is $libraryAdSlot and starting position greater than $min
     *
     * @param LibraryDisplayAdSlotInterface $libraryAdSlot
     * @param $min
     * @param null $limit
     * @param null $offset
     *
     * @return LibraryExpressionInterface[]
     */
    public function getByLibraryAdSlotAndStartingPosition(LibraryDisplayAdSlotInterface $libraryAdSlot, $min, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('le')
            ->where('le.expectLibraryAdSlot = :library_ad_slot_id')
            ->andWhere('le.startingPosition IS NOT null AND le.startingPosition > :position')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->setParameter('position', $min, Type::INTEGER)
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
     * Get all library expressions that has expect library slot refer to the given library ad slot
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSLot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByExpectLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSLot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('le')
            ->where('le.expectLibraryAdSlot = :library_ad_slot_id')
            ->setParameter('library_ad_slot_id', $libraryAdSLot->getId(), Type::INTEGER)
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