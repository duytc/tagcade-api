<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getChannelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->getChannelsForPublisherQuery($publisher, $limit, $offset);
        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function getChannelsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('cl')
            ->where('cl.publisher = :publisher_id')
            ->setParameter('publisher_id', $publisher->getId(), Type::INTEGER)
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