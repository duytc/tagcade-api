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
     * @var LibraryDisplayAdSlotInterface
     */
    protected $libraryDisplayAdSlot;

    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;

    protected $name;
    protected $width;
    protected $height;

    public function __construct()
    {
    }
}