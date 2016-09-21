<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\WhiteListInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class WhiteListRepository extends EntityRepository implements WhiteListRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getWhiteListsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r')
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

    public function findWhiteListBySuffixKey($suffixKey)
    {
        return $this->createQueryBuilder('r')
            ->where('r.suffixKey = :suffix_key')
            ->setParameter('suffix_key', $suffixKey)
            ->getQuery()->getOneOrNullResult();
    }
}