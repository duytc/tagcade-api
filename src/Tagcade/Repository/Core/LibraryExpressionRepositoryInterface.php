<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

interface LibraryExpressionRepositoryInterface extends ObjectRepository {
    /**
     * Get all library expressions that have expect library ad slot is $libraryAdSlot and starting position greater than $min
     *
     * @param LibraryDisplayAdSlotInterface $libraryAdSlot
     * @param $min
     * @param null $limit
     * @param null $offset
     *
     * @return LibraryExpressionInterface[]
     */
    public function getByLibraryAdSlotAndStartingPosition(LibraryDisplayAdSlotInterface $libraryAdSlot, $min, $limit = null, $offset = null);
} 