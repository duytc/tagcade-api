<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayAdSlot as DisplayAdSlotModel;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;

class DisplayAdSlot extends DisplayAdSlotModel
{
    protected $id;
    protected $site;
    protected $deletedAt;

    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;

    public function __construct()
    {
    }
}