<?php

namespace Tagcade\Repository\Core;
use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

interface LibrarySlotTagRepositoryInterface extends ObjectRepository{
    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param int|null $limit
     * @param int|null $offset
     * @return LibrarySlotTagInterface[]
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param LibraryAdTagInterface $adTag
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndLibraryAdTagAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, LibraryAdTagInterface $adTag, $refId);
}