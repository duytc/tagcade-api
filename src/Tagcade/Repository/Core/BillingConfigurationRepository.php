<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Tagcade\Model\User\Role\PublisherInterface;

class BillingConfigurationRepository extends EntityRepository implements BillingConfigurationRepositoryInterface
{
    public function getAllConfigurationForPublisher(PublisherInterface $publisher)
    {
        return $this->createQueryBuilder('r')
            ->where('r.publisher = :publisher')
            ->setParameter('publisher', $publisher)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getConfigurationForModule(PublisherInterface $publisher, $module)
    {
        return $this->createQueryBuilder('r')
            ->where('r.publisher = :publisher')
            ->andWhere('r.module = :module')
            ->setParameter('publisher', $publisher)
            ->setParameter('module', $module)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}