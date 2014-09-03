<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\SiteInterface;

class AdSlotRepository extends EntityRepository implements AdSlotRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.site = :site_id')
            ->setParameter('site_id', $site->getId(), Type::INTEGER)
            ->addOrderBy('s.id', 'asc')
        ;

        if (is_int($limit)) {
            $qb->setMaxResults($limit);
        }

        if (is_int($offset)) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}