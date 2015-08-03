<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlot as LibraryDynamicAdSlotModel;

class LibraryDynamicAdSlot extends LibraryDynamicAdSlotModel
{
    protected $id;
    protected $name;
    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $dynamicAdSlots;
    /**
     * @var BaseAdSlotInterface
     */
    protected $defaultAdSlot;
    protected $libraryAdTags;
    protected $expressions;
    protected $visible;
    protected $native;

    public function __construct()
    {
        $this->expressions = new ArrayCollection();
    }
}