<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class AdNetworkPartnerRepository extends EntityRepository implements AdNetworkPartnerRepositoryInterface
{
    /**
     * @param int $publisherId
     * @return array
     */
    public function findByPublisher($publisherId)
    {
        $qb = $this->getPartnerForPublisherQuery($publisherId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    protected function getPartnerForPublisherQuery($publisherId, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('np')
            ->join('np.publisherPartners', 'pub')
            ->where('pub.publisherId = :publisher_id')
            ->setParameter('publisher_id', $publisherId, Type::INTEGER)
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