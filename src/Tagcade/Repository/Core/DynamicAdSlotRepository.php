<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DynamicAdSlotRepository extends EntityRepository implements DynamicAdSlotRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'channel' => 'channel',
        'domain' => 'domain',
        'size' => 'size',
        'type' => 'type',
        'rtb' => 'rtb'
    ];

    /**
     * @inheritdoc
     */
    public function getDynamicAdSlotsThatHaveDefaultAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.defaultAdSlot = :ad_slot_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER);

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
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.id = :id')
            ->setParameter('id', $id, Type::INTEGER)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     */
    public function findAll($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl');

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
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @inheritdoc
     */
    public function getAdSlotsForUserWithPagination(UserRoleInterface $user, PagerParam $param = null)
    {
        $qb = $this->createQueryBuilderForUser($user);
        if ($user instanceof AdminInterface) {
            $qb->join('sl.site', 'st');
        }

        $qb->join('sl.libraryAdSlot', 'lsl');

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->like('lsl.name', ':searchKey'),
                    $qb->expr()->like('sl.id', ':searchKey'),
                    $qb->expr()->like('st.name', ':searchKey'), $qb->expr()->like('st.domain', ':searchKey')
                ))
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
                    $qb->addOrderBy('lsl.' . $param->getSortField(), $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['domain']:
                    $qb->addOrderBy('st.' . 'name', $param->getSortDirection());
                    break;
                case $this->SORT_FIELDS['rtb']:
                    $qb->addOrderBy('st.' . 'rtbStatus', $param->getSortDirection());
                    break;
                default:
                    break;
            }
        }

        return $qb;
    }

    /**
     * @param UserRoleInterface $user
     * @return QueryBuilder
     */
    private function createQueryBuilderForUser(UserRoleInterface $user)
    {
        return $user instanceof PublisherInterface ? $this->createQueryBuilderForPublisher($user) : $this->createQueryBuilder('sl');
    }

    /**
     * create QueryBuilder For Publisher due to Publisher or SubPublisher
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder qb with alias 'sl'
     */
    protected function createQueryBuilderForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl')
            ->leftJoin('sl.site', 'st');

        if ($publisher instanceof SubPublisherInterface) {
            $qb
                ->where('st.subPublisher = :sub_publisher_id')
                ->setParameter('sub_publisher_id', $publisher->getId(), Type::INTEGER);
        } else {
            $qb
                ->where('st.publisher = :publisher_id')
                ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER);
        }

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }
}