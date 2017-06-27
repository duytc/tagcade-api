<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryExpressionRepository extends EntityRepository implements LibraryExpressionRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
    ];

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

    public function getLibraryExpressionsForUserWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('le');

        if ($user instanceof PublisherInterface && !$user instanceof SubPublisherInterface) {
            $qb->join('le.libraryDynamicAdSlot', 'libSlot')
                ->where('libSlot.publisher = :publisher')
                ->setParameter('publisher', $user);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->like('le.name', ':searchKey'))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('le.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    public function getLibraryExpressionsForPublisher(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('le')
            ->join('le.libraryDynamicAdSlot', 'libSlot')
            ->where('libSlot.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->getQuery()
            ->getResult()
        ;
    }
}