<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdSlotLibAbstract;
use Tagcade\Entity\Core\LibraryAdSlotAbstract;
use Tagcade\Model\Core\SiteInterface;

class LibraryNativeAdSlot extends LibraryAdSlotAbstract implements LibraryNativeAdSlotInterface
{
    protected $id;

    /**
     * @var NativeAdSlotInterface[]
     */
    protected $nativeAdSlots;
    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $defaultDynamicAdSlots;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }


    /**
     * @return DynamicAdSlotInterface[]
     */
    public function defaultDynamicAdSlots()
    {
        return $this->defaultDynamicAdSlots;
    }


    public function __toString()
    {
        return parent::__toString();
    }

    /**
     * @return NativeAdSlotInterface[]
     */
    public function getNativeAdSlots()
    {
        return $this->nativeAdSlots;
    }

    /**
     * @param NativeAdSlotInterface[] $nativeAdSlots
     */
    public function setNativeAdSlots($nativeAdSlots)
    {
        $this->nativeAdSlots = $nativeAdSlots;
    }

    /**
     * @return string|null
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * @param string $referenceName
     * @return self
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;
    }


    public function isReferenced() {
        return $this->nativeAdSlots != null && $this->nativeAdSlots->count() > 0;
    }

    public function getLibType()
    {
        return self::TYPE_NATIVE;
    }

    public function getAdSlots()
    {
        return $this->nativeAdSlots;
    }


}