<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayBlacklistInterface;

class NetworkBlacklistRepository extends EntityRepository implements NetworkBlacklistRepositoryInterface
{
    /**
     * @param DisplayBlacklistInterface $displayBlacklist
     * @return array
     */
    public function getForDisplayBlacklist(DisplayBlacklistInterface $displayBlacklist)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.displayBlacklist = :blacklist')
            ->setParameter('blacklist', $displayBlacklist)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return array
     */
    public function getForAdNetwork(AdNetworkInterface $adNetwork)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork)
            ->getQuery()
            ->getResult();
    }
}