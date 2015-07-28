<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlot as LibraryNativeAdSlotModel;
use Tagcade\Model\Core\NativeAdSlotInterface;

class LibraryNativeAdSlot extends LibraryNativeAdSlotModel
{
    protected $id;
    protected $referenceName;
    protected $visible;
    /**
     * @var NativeAdSlotInterface[]
     */
    protected $nativeAdSlots;
    public function __construct()
    {}

}