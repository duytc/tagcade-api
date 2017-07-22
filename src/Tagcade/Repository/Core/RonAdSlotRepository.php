<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class RonAdSlotRepository extends EntityRepository implements RonAdSlotRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'name' => 'name', 'publisher' => 'publisher', 'type' => 'type',
        'segment' => 'segment'];

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

    /**
     * @inheritdoc
     */
    public function getRonAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param)
    {

        $qb = $this->createQueryBuilder('ra')
            ->leftJoin('ra.libraryAdSlot', 'la');

        if ($user instanceof PublisherInterface) {
            $qb
                ->where('la.publisher = :publisher_id')
                ->setParameter('publisher_id', $user->getId(), TYPE::INTEGER);
        }

        $qb->leftjoin('la.publisher', 'pls');
        $qb->leftjoin('ra.ronAdSlotSegments', 'rasg');
        $qb->leftJoin('rasg.segment', 'sg');


        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX($qb->expr()->like('la.name', ':searchKey'), $qb->expr()->orX($qb->expr()->like('pls.company', ':searchKey'))))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('ra.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('la.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['publisher']:
                    $qb->addOrderBy('pls.' . 'company', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['type']:
                    break;
                case $this->SORT_FIELDS['segment']:
                    $qb->addOrderBy('sg.' . $param->getSortField(), $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }
        return $qb;
    }
}