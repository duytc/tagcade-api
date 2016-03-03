<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\ExchangeInterface;

class ExchangeRepository extends EntityRepository implements ExchangeRepositoryInterface
{
    /**
     * @param $name
     * @return ExchangeInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExchangeByName($name)
    {
        return $qb = $this->createQueryBuilder('ex')
            ->where('ex.name = :name')
            ->setParameter('name', $name, Type::STRING)
            ->getQuery()->getOneOrNullResult()
        ;
    }

    /**
     * @param $name
     * @return ExchangeInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getExchangeByCanonicalName($name)
    {
        return $qb = $this->createQueryBuilder('ex')
            ->where('ex.canonicalName = :name')
            ->setParameter('name', $name, Type::STRING)
            ->getQuery()->getOneOrNullResult()
            ;
    }
}