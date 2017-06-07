<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

interface ReplicatorInterface {
    /**
     * Replicate data from library ad slot to an ad slot
     *
     * @param BaseLibraryAdSlotInterface $libAdSlot
     * @param BaseAdSlotInterface $adSlot
     * @return mixed
     */
    public function replicateFromLibrarySlotToSingleAdSlot(BaseLibraryAdSlotInterface $libAdSlot, BaseAdSlotInterface &$adSlot);

    /**
     * add new ad tag to all slots that refer to the same library slot that is persisting new $librarySlotTag
     *
     * @param LibrarySlotTagInterface $librarySlotTag
     * @return AdTagInterface[]|null
     */
    public function replicateNewLibrarySlotTagToAllReferencedAdSlots(LibrarySlotTagInterface $librarySlotTag);

    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @param bool $remove true if we are removing $librarySlotTag
     */
    public function replicateExistingLibrarySlotTagToAllReferencedAdTags(LibrarySlotTagInterface $librarySlotTag, $remove = false);

    /**
     * Replicate Default Library AdSlot and Expression to all referenced Dynamic AdSLot
     *
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @return mixed
     */
    public function replicateLibraryDynamicAdSlotForAllReferencedDynamicAdSlots(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot);

    public function replicateExistingLibrarySlotTagsToAllReferencedAdTags(array $librarySlotTags, $remove = false);
}