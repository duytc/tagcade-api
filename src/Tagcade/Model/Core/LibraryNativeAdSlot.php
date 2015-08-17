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
}