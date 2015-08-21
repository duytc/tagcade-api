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
     * ReferenceId is unique in each library ad slot so this query always return only one entity or null
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId);
}