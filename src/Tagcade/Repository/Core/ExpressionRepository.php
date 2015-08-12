<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\BaseAdSlotInterface;

class ExpressionRepository extends EntityRepository implements ExpressionRepositoryInterface {

    /**
     * Get those expressions that refer to an AdSlot with the given starting position
     *
     * @param BaseAdSlotInterface $adSlot
     * @param $position
     * @return ArrayCollection
     */
    public function getByExpectAdSlotAndStartingPosition(BaseAdSlotInterface $adSlot, $position)
    {
        $qb = $this->createQueryBuilder('exp')
            ->leftJoin('exp.libraryExpression', 'lib_exp')
            ->where('exp.expectAdSlot = :expect_ad_slot_id')
            ->andWhere('lib_exp.startingPosition = :starting_position')
            ->setParameter('expect_ad_slot_id', $adSlot->getId(), Type::INTEGER)
            ->setParameter('starting_position', $position, Type::INTEGER)
        ;

        return $qb->getQuery()->getResult();
    }
}