<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryNativeAdSlot as LibraryNativeAdSlotModel;

class LibraryNativeAdSlot extends LibraryNativeAdSlotModel
{
    protected $id;
    protected $name;
    protected $ronAdSlot;
    protected $publisher;
    protected $visible;
    protected $libraryAdTags;
    protected $nativeAdSlots;
    public function __construct()
    {}

}