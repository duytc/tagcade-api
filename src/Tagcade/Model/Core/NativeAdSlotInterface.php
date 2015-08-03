<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface NativeAdSlotInterface extends BaseAdSlotInterface
{
    /**
     * @return ArrayCollection
     */
    public function getAdTags();

    /**
     * @param ArrayCollection $adTags
     */
    public function setAdTags($adTags);

    /**
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function getDefaultLibraryDynamicAdSlots();

    /**
     * @param LibraryDynamicAdSlotInterface[] $defaultLibraryDynamicAdSlots
     */
    public function setDefaultLibraryDynamicAdSlots($defaultLibraryDynamicAdSlots);

    /**
     * get the list of NativeAdSlot that also refers to the NativeAdSlotLib of this entity
     * @return NativeAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots();

}