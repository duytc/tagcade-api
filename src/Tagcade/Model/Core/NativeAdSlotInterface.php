<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;

interface NativeAdSlotInterface extends BaseAdSlotInterface
{
    /**
     * @return ArrayCollection
     */
    public function getAdTags();

    /**
     * @param ArrayCollection $adTags
     * @return self
     */
    public function setAdTags($adTags);

    /**
     * get the list of NativeAdSlot that also refers to the NativeAdSlotLib of this entity
     * @return NativeAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();
}