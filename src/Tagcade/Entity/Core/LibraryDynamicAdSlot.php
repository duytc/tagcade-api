<?php

namespace Tagcade\Entity\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\Core\LibraryDynamicAdSlot as LibraryDynamicAdSlotModel;

class LibraryDynamicAdSlot extends LibraryDynamicAdSlotModel
{
    protected $id;
    protected $name;
    protected $dynamicAdSlots;
    protected $libraryExpressions;
    protected $defaultLibraryAdSlot;
    protected $visible;
    protected $native;

    public function __construct()
    {
        $this->libraryExpressions = new ArrayCollection();
    }
}