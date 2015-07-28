<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface DynamicAdSlotInterface extends BaseAdSlotInterface
{
    /**
     * @return BaseAdSlotInterface
     */
    public function getDefaultAdSlot();

    /**
     * @param BaseAdSlotInterface $defaultAdSlot
     */
    public function setDefaultAdSlot($defaultAdSlot);

    /**
     * @return ArrayCollection
     */
    public function getExpressions();
    /**
     * @param ExpressionInterface[] $expressions
     */
    public function setExpressions($expressions);

    /**
     * @return boolean
     */
    public function isSupportedNative();

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryDynamicAdSlot();

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     */
    public function setLibraryDynamicAdSlot($libraryDynamicAdSlot);

    /**
     * @return LibraryDynamicAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * get the list of DynamicAdSlot that also refers to the DynamicAdSlotLib of this entity
     * @return DynamicAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();
}