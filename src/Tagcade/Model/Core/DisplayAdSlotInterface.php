<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @return LibraryDisplayAdSlotInterface
     */
    public function getLibraryDisplayAdSlot();

    /**
     * @return LibraryDisplayAdSlotInterface
     */
    public function getLibraryAdSlot();

    /**
     * @param LibraryDisplayAdSlotInterface $libraryDisplayAdSlot
     * @return mixed
     */
    public function setLibraryDisplayAdSlot(LibraryDisplayAdSlotInterface $libraryDisplayAdSlot);


    /**
     * get the list of DisplayAdSlot that also refers to the DisplayAdSlotLib of this entity
     * @return DisplayAdSlotInterface[]
     */
    public function getCoReferencedAdSlots();

    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots();
}