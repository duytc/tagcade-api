<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class NetworkBlacklistRepository extends EntityRepository implements NetworkBlacklistRepositoryInterface
{
    public function getAdNetworksForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.displayBlacklist = :blacklist')
            ->setParameter('blacklist', $displayBlacklist)
            ->getQuery()
            ->getResult();
    }

    public function getDefaultNetworkForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.displayBlacklist = :blacklist')
            ->andWhere('nb.adNetwork is NULL')
            ->setParameter('blacklist', $displayBlacklist)
            ->getQuery()
            ->getResult();
    }
}