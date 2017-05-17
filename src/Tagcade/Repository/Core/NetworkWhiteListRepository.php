<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class NetworkWhiteListRepository extends EntityRepository implements NetworkWhiteListRepositoryInterface
{
    /**
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function all($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('r');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param DisplayWhiteListInterface $displayWhiteList
     * @return array
     */
    public function getForDisplayWhiteList(DisplayWhiteListInterface $displayWhiteList)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.displayWhiteList = :whiteList')
            ->setParameter('whiteList', $displayWhiteList)
            ->getQuery()
            ->getResult();
    }

    public function getForAdNetwork(AdNetworkInterface $adNetwork)
    {
        return $this->createQueryBuilder('nb')
            ->where('nb.adNetwork = :adNetwork')
            ->setParameter('adNetwork', $adNetwork)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getNetworkWhiteListForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('nwl')
            ->join('nwl.adNetwork', 'nw')
            ->where('nw.publisher = :publisher')
            ->setParameter('publisher', $publisher);

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }


}