<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface NativeAdSlotInterface extends AdSlotAbstractInterface
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
     * @return ArrayCollection
     */
    public function defaultDynamicAdSlots();

}