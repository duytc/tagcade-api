<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class DisplayWhiteListRepository extends EntityRepository implements DisplayWhiteListRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'publisher' => 'publisher'
    ];

    /**
     * @param $limit
     * @param $offset
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
    public function getDisplayWhiteListsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.networkWhiteLists', 'rn')
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


    /**
     * @param AdNetworkInterface $adNetwork
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getWhiteListsForAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.networkWhiteLists', 'rn')
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

    public function getDisplayWhiteListsForPublisherWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('dw');

        if ($user instanceof PublisherInterface && !$user instanceof SubPublisherInterface) {
            $qb->where('dw.publisher = :publisher')
                ->setParameter('publisher', $user);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX (
                $qb->expr()->like('dw.name', ':searchKey'),
                $qb->expr()->like('dw.id', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('dw.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    public function getDisplayWhiteListsForAdNetworkWithPagination(AdNetworkInterface $adNetwork, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('dw')
            ->join('dw.networkWhiteLists', 'nwl')
            ->where('nwl.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork);

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->orX (
                $qb->expr()->like('dw.name', ':searchKey'),
                $qb->expr()->like('dw.id', ':searchKey')
            ))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('dw.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    /**
     * @inheritdoc
     */
    public function getWhitelistForAdSlot(BaseAdSlotInterface $adSlot)
    {
        $qb = $this->createQueryBuilder('dwl')
            ->join('dwl.networkWhiteLists', 'nwl')
            ->join('nwl.adNetwork', 'adnw')
            ->join('adnw.libraryAdTags', 'lt')
            ->join('lt.adTags', 't')
            ->join('t.adSlot', 'adslot')
            ->where('adslot.id = :adslotID')
            ->setParameter('adslotID', $adSlot->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getWhitelistForLibAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot)
    {
        $qb = $this->createQueryBuilder('dwl')
            ->join('dwl.networkWhiteLists', 'nwl')
            ->join('nwl.adNetwork', 'adnw')
            ->join('adnw.libraryAdTags', 'lt')
            ->join('lt.libSlotTags', 'lst')
            ->join('lst.libraryAdSlot', 'las')
            ->where('las.id = :libAdSlotId')
            ->setParameter('libAdSlotId', $libraryAdSlot->getId());

        return $qb->getQuery()->getResult();
    }

}