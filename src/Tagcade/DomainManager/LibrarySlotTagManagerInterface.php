<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

interface LibrarySlotTagManagerInterface extends ManagerInterface
{
    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);

    /**
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId);

    /**
     * get all librarySlotTags By LibraryAdSlot And Differ RefId (not include the ad tag with refId)
     *
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndDifferRefId(BaseLibraryAdSlotInterface $libraryAdSlot, $refId);
}