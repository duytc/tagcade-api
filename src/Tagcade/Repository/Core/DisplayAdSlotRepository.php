<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\SiteInterface;

class DisplayAdSlotRepository extends EntityRepository implements DisplayAdSlotRepositoryInterface
{
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

    public function getAdSlotForSiteByName(SiteInterface $site, $name)
    {
        return $this->createQueryBuilder('d')
            ->join('d.libraryAdSlot', 'l')
            ->where('d.site = :site_id')
            ->andWhere('l.name = :name')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->setParameter('name', $name, Type::STRING)
            ->getQuery()->getOneOrNullResult();
    }

    public function deleteAdSlotForSite(SiteInterface $site)
    {
        return $this->_em->getConnection()->executeUpdate(
            'UPDATE core_ad_slot set deleted_at = NOW() where site_id = :site_id',
            array(
                'site_id' => $site->getId()
            )
        );
    }
}