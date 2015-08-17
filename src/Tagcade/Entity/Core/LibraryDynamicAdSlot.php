<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlot as LibraryDynamicAdSlotModel;
use Tagcade\Model\Core\LibraryExpressionInterface;

class LibraryDynamicAdSlot extends LibraryDynamicAdSlotModel
{
    protected $id;
    protected $name;
    /**
     * @var DynamicAdSlotInterface[]
     */
    protected $dynamicAdSlots;
    /**
     * @var LibraryExpressionInterface[]
     */
    protected $libraryExpressions;

    /**
     * @var BaseLibraryAdSlotInterface
     */
    protected $defaultLibraryAdSlot;
    protected $visible;
    protected $native;

    public function __construct()
    {
        $this->libraryExpressions = new ArrayCollection();
    }
}