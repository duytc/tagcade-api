<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\NetworkBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DisplayBlacklistRepository extends EntityRepository implements DisplayBlacklistRepositoryInterface
{
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
}