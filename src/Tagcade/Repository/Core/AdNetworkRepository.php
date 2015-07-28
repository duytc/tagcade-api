<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class AdNetworkRepository extends EntityRepository implements AdNetworkRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getAdNetworksForPublisherQuery($publisher, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getAdNetworksForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
            ->addOrderBy('n.name', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }
}