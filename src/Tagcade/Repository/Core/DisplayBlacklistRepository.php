<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DisplayBlacklistRepository extends EntityRepository implements DisplayBlacklistRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'publisher' => 'publisher'
    ];

    /**
     * @return array
     */
    public function all($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.publisher IS NOT NULL');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getDisplayBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.networkBlacklists', 'rn')
            ->where('r.publisher = :publisher')
            ->setParameter('publisher', $publisher);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function findDisplayBlacklistsByNameForPublisher(PublisherInterface $publisher, $name, $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.networkBlacklists', 'rn')
            ->where('r.publisher = :publisher')
            ->andWhere('r.name = :name')
            ->setParameter('publisher', $publisher)
            ->setParameter('name', $name);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getBlacklistsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.networkBlacklists', 'rn')
            ->where('rn.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDefaultBlacklists(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isDefault = :isDefault')
            ->andWhere('a.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->setParameter('isDefault', true);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param NetworkBlacklistInterface $networkBlacklist
     * @return mixed
     */
    public function getByNetworkBlacklist(NetworkBlacklistInterface $networkBlacklist)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.networkBlacklists', 'nb')
            ->andWhere('nb.id = :id')
            ->setParameter('id', $networkBlacklist->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return QueryBuilder
     */
    public function getDisplayBlacklistsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('db');

        if ($user instanceof PublisherInterface && !$user instanceof SubPublisherInterface) {
            $qb->where('db.publisher = :publisher')
                ->setParameter('publisher', $user);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX (
                $qb->expr()->like('db.name', ':searchKey'),
                $qb->expr()->like('db.id', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('db.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PagerParam $param
     * @return mixed
     */
    public function getDisplayBlacklistsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('db')
            ->join('db.networkBlacklists', 'nbl')
            ->where('nbl.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX (
                $qb->expr()->like('db.name', ':searchKey'),
                $qb->expr()->like('db.id', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('db.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getBlacklistForAdSlot(BaseAdSlotInterface $adSlot)
    {
        $qb = $this->createQueryBuilder('dbl')
            ->join('dbl.networkBlacklists', 'nbl')
            ->join('nbl.adNetwork', 'adnw')
            ->join('adnw.libraryAdTags', 'lt')
            ->join('lt.adTags', 't')
            ->join('t.adSlot', 's')
            ->where('s.id = :adSlotId')
            ->setParameter('adSlotId', $adSlot->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getBlacklistForLibAdSlot(BaseLibraryAdSlotInterface $libAdSlot)
    {
        $qb = $this->createQueryBuilder('dbl')
            ->join('dbl.networkBlacklists', 'nbl')
            ->join('nbl.adNetwork', 'adnw')
            ->join('adnw.libraryAdTags', 'lt')
            ->join('lt.libSlotTags', 'lst')
            ->join('lst.libraryAdSlot', 'las')
            ->where('las.id = :libAdSlotId')
            ->setParameter('libAdSlotId', $libAdSlot->getId());

        return $qb->getQuery()->getResult();
    }

}