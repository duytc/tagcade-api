<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

interface DisplayAdSlotInterface extends BaseAdSlotInterface
{
    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getHeight();

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return PersistentCollection
     */
    public function getAdTags();

    /**
     * @param ArrayCollection $adTags
     */
    public function setAdTags($adTags);

    /**
     * @return BaseLibraryAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * get the list of DisplayAdSlot that also refers to the DisplayAdSlotLib of this entity
     * @return DisplayAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();
}