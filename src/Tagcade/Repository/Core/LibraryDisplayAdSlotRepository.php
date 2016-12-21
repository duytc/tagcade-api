<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class LibraryDisplayAdSlotRepository extends EntityRepository implements LibraryDisplayAdSlotRepositoryInterface
{
    protected $SORT_FIELDS = ['id' => 'id', 'size' => 'size', 'name' => 'name', 'publisher' => 'publisher', 'type' => 'type',
        'deployment' => 'deployment', 'ronAdSlot' => 'ronAdSlot'];

    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getLibraryDisplayAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
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
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryDisplayAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);

        return $qb->getQuery()->getResult();
    }

    public function getLibraryDisplayAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getLibraryDisplayAdSlotsForPublisherQuery($publisher, $limit = null, $offset = null);
        $qb->andWhere('sl.ronAdSlot IS NULL');

        return $qb->getQuery()->getResult();
    }

    public function getAllLibraryDisplayAdSlotsForPublisherQuery(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
        ;
    }

    public function getLibraryAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('sl')
            ->andWhere('sl.visible = :visible')
            ->setParameter('visible', true, Type::BOOLEAN);

        if ($user instanceof PublisherInterface) {
            // get all library ad slots that used for SHARING, without order
            $qb = $this->getLibraryDisplayAdSlotsForPublisherQuery($user);
        }

        $qb->leftJoin('sl.publisher', 'pls');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('sl.name', ':searchKey'),
                $qb->expr()->like('sl.id', ':searchKey'),
                $qb->expr()->like('pls.company', ':searchKey'))
            )
            ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            switch ($param->getSortField()) {
                case $this->SORT_FIELDS['id']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['name']:
                    $qb->addOrderBy('sl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['publisher']:
                    $qb->addOrderBy('pls.' . 'company', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }
}