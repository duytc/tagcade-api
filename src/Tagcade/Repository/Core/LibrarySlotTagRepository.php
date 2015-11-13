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
        $qb->orderBy('lst.position', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function getLibrarySlotTagIdsByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->getByLibraryAdSlotQuery($libraryAdSlot, $limit, $offset);

        $results = $qb->select('lst.id')->getQuery()->getArrayResult();

        return array_map(function($resultItem) { return $resultItem['id']; }, $results);
    }

    protected function getByLibraryAdSlotQuery(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('lst')
            ->where('lst.libraryAdSlot = :library_ad_slot_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
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
     * ReferenceId is unique in each library ad slot so this query always return only one entity or null
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface[]
     */
    public function getByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId)
    {
        $qb = $this->createQueryBuilder('lst')
            ->where('lst.libraryAdSlot = :library_ad_slot_id')
            ->andWhere('lst.refId = :ref_id')
            ->setParameter('library_ad_slot_id', $libraryAdSlot->getId(), Type::INTEGER)
            ->setParameter('ref_id', $refId, Type::STRING)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}