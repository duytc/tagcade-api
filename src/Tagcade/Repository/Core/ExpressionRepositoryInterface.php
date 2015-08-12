<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\BaseAdSlotInterface;

interface ExpressionRepositoryInterface extends ObjectRepository {
    /**
     * Get those expressions that refer to an AdSlot with the given starting position
     *
     * @param BaseAdSlotInterface $adSlot
     * @param $position
     * @return mixed
     */
    public function getByExpectAdSlotAndStartingPosition(BaseAdSlotInterface $adSlot, $position);
} 