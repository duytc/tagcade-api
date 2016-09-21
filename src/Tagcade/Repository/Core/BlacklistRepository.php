<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Entity\Core\Blacklist;
use Tagcade\Model\User\Role\PublisherInterface;

class BlacklistRepository extends EntityRepository implements BlacklistRepositoryInterface
{
    /**
     * @var array
     */
    private $builtinBlacklists;

    public function setBuiltinBlacklist(array $builtinBlacklists)
    {
        $this->builtinBlacklists = $builtinBlacklists;
    }

    /**
     * @inheritdoc
     */
    public function getBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
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

    /**
     * @inheritdoc
     */
    public function findBlacklistBySuffixKey($suffixKey)
    {
        return $this->createQueryBuilder('r')
            ->where('r.suffixKey = :suffix')
            ->setParameter('suffix', $suffixKey)
            ->getQuery()->getOneOrNullResult();
    }
}