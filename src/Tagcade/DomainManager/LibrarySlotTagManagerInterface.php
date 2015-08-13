<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
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
     * @param LibraryAdTagInterface $libraryAdTag
     * @param $refId
     * @return LibrarySlotTagInterface|null
     */
    public function getByLibraryAdSlotAndLibraryAdTagAndRefId(BaseLibraryAdSlotInterface $libraryAdSlot, LibraryAdTagInterface $libraryAdTag, $refId);
}