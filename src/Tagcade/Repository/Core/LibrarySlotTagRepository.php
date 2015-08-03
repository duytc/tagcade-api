<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class LibrarySlotTagRepository extends EntityRepository implements LibrarySlotTagRepositoryInterface {


    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return LibrarySlotTagInterface[]
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->getByLibraryAdSlotQuery($libraryAdSlot, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function getByLibraryAdSlotQuery(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('lst')
            ->where('lst.libraryAdSlot = :library_ad_slot_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->orderBy('lst.position', 'asc')
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
     * @param LibraryAdTagInterface $adTag
     * @param $refId
     * @return LibrarySlotTagInterface[]
     */
    public function getByLibraryAdSlotAndLibraryAdTagAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, LibraryAdTagInterface $adTag, $refId)
    {
        $qb = $this->createQueryBuilder('lst')
            ->where('lst.libraryAdSlot = :library_ad_slot_id')
            ->andWhere('lst.libraryAdTag = :library_ad_tag_id')
            ->andWhere('lst.refId = :ref_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->setParameter('library_ad_tag_id', $adTag->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}