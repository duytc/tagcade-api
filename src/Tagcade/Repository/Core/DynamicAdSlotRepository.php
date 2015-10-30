<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\ReportableAdSlotInterface;

class DynamicAdSlotRepository extends EntityRepository implements DynamicAdSlotRepositoryInterface
{

    public function getDynamicAdSlotsThatHaveDefaultAdSlot(ReportableAdSlotInterface $adSlot, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('d')
            ->where('d.defaultAdSlot = :ad_slot_id')
            ->setParameter('ad_slot_id', $adSlot->getId(), Type::INTEGER)
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->createQueryBuilder('sl')
            ->where('sl.id = :id')
            ->setParameter('id', $id, TYPE::INTEGER)
            ->getQuery()->getOneOrNullResult();
    }

    public function findAll($limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('sl');

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


}