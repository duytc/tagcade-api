<?php

namespace Tagcade\Repository\Report\PerformanceReport\Display;

use Doctrine\ORM\EntityRepository;
use Tagcade\Entity\Report\PerformanceReport\Display\CPMRateDisplayAdTag;
use Tagcade\Model\Core\AdTagInterface;
use DateTime;

class CPMRateRepository extends EntityRepository implements CPMRateRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getRateByAdTag(AdTagInterface $adTag)
    {
        $qb = $this->createQueryBuilder('r');
        /**
         * @var CPMRateDisplayAdTag
         */
        $record = $qb->where('r.adTag = :adTag')
            ->andWhere('r.date <= :today')
            ->setParameter('adTag', $adTag)
            ->setParameter('today', new DateTime())
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;

        return $record->getRate();
    }


} 