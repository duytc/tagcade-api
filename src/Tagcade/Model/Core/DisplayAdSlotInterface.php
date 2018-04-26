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
     * @return boolean
     */
    public function isAutoFit();

    /**
     * @param int $autoFit
     * @return self
     */
    public function setAutoFit($autoFit);

    /**
     * @return string
     */
    public function getPassbackMode();

    /**
     * @param string $passbackMode
     * @return self
     */
    public function setPassbackMode($passbackMode);

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

    /**
     * @return mixed
     */
    public function getHbBidPrice();

    /**
     * @param mixed $hbBidPrice
     * @return self
     */
    public function setHbBidPrice($hbBidPrice);

    /**
     * @return boolean
     */
    public function isAutoOptimize();

    /**
     * @param boolean $autoOptimize
     * @return self
     */
    public function setAutoOptimize($autoOptimize);
}