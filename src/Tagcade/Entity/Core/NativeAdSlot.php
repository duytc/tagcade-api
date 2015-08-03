<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlot as NativeAdSlotModel;
use Tagcade\Model\Core\SiteInterface;

class NativeAdSlot extends NativeAdSlotModel
{
    protected $id;
    protected $site;
    protected $deletedAt;
    /**
     * @var LibraryDynamicAdSlotInterface[]
     */
    protected $defaultLibraryDynamicAdSlots;
    public function __construct()
    {}


}