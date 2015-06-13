<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\NativeAdSlot as NativeAdSlotModel;

class NativeAdSlot extends NativeAdSlotModel
{
    protected $id;
    protected $site;
    protected $name;
    protected $deletedAt;

    public function __construct()
    {}
}