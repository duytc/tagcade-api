<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

class AdNetworkRepository extends EntityRepository implements AdNetworkRepositoryInterface
{
    protected $SORT_FIELDS = [
        'id' => 'id',
        'name' => 'name',
        'networkOpportunityCap' => 'networkOpportunityCap'
    ];

    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdNetworksForPublisherQuery($publisher, $limit, $offset)
            ->addOrderBy('n.name', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworksForActivePublishers()
    {
        $qb = $this->createQueryBuilder('adNetwork')
            ->leftJoin('adNetwork.publisher', 'publisher')
            ->where('publisher.enabled = 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function allHasCap($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where($qb->expr()->gt('n.networkOpportunityCap', 0))
            ->orWhere($qb->expr()->gt('n.impressionCap', 0));
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
    public function getAdNetworksForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $publisherId = ($publisher instanceof SubPublisherInterface) ? $publisher->getPublisher()->getId() : $publisher->getId();

        $qb = $this->createQueryBuilder('n')
            ->where('n.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER);

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
    public function getAdNetworksForUserWithPagination(UserRoleInterface $user, PagerParam $param)
    {
        $qb = $this->createQueryBuilder('nw');

        if ($user instanceof PublisherInterface && !$user instanceof SubPublisherInterface) {
            $qb->where('nw.publisher = :publisher')
                ->setParameter('publisher', $user);
        }

        if (is_string($param->getSearchKey())) {
            $searchLike = sprintf('%%%s%%', $param->getSearchKey());
            $qb->andWhere($qb->expr()->like('nw.name', ':searchKey'))
                ->setParameter('searchKey', $searchLike);
        }

        if (is_string($param->getSortField()) &&
            is_string($param->getSortDirection()) &&
            in_array($param->getSortDirection(), ['asc', 'desc', 'ASC', 'DESC']) &&
            in_array($param->getSortField(), $this->SORT_FIELDS)
        ) {
            $qb->addOrderBy('nw.' . $param->getSortField(), $param->getSortDirection());
        }

        return $qb;
    }

    public function getAdNetworksForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist, $limit = null, $offset = null)
    {
        return $this->createQueryBuilder('nw')
            ->leftJoin('nw.networkBlacklists', 'nb')
            ->where('nb.displayBlacklist = :blacklist')
            ->setParameter('blacklist', $displayBlacklist)
            ->getQuery()
            ->getResult();
    }
}