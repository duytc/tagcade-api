<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryDisplayAdSlot as LibraryDisplayAdSlotModel;

class LibraryDisplayAdSlot extends LibraryDisplayAdSlotModel
{
    protected $id;
    protected $name;
    protected $width;
    protected $height;
    protected $visible;
    protected $libSlotTags;
    protected $publisher;
    protected $displayAdSlots;

    public function __construct()
    {}
}