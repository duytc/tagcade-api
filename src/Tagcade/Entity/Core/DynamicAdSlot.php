<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlot as DynamicAdSlotModel;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class DynamicAdSlot extends DynamicAdSlotModel
{
    protected $id;
    protected $site;

    public function __construct()
    {}


}