<?php

namespace Tagcade\Service\TagLibrary;


use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
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
     * @param LibrarySlotTagInterface $librarySlotTag
     *
     * @return AdTagInterface[]|null
     */
    public function replicateNewLibrarySlotTagToAllReferencedAdSlots(LibrarySlotTagInterface $librarySlotTag);


    /**
     * @param LibrarySlotTagInterface $librarySlotTag
     * @param bool $remove true if we are removing $librarySlotTag
     */
    public function replicateExistingLibrarySlotTagToAllReferencedAdTags(LibrarySlotTagInterface $librarySlotTag, $remove = false);


    /**
     * Replicate single library expression for all referenced dynamic ad slots
     * @param LibraryExpressionInterface $libraryExpression
     * @return mixed
     */
    public function replicateLibraryExpressionForAllReferencedDynamicAdSlots(LibraryExpressionInterface $libraryExpression);

    /**
     * Replicate multiple library expressions for all referenced dynamic ad slots
     *
     * @param array $libraryExpressions
     * @return mixed
     */
    public function replicateLibraryExpressionsForAllReferencedDynamicAdSlots(array $libraryExpressions);

} 