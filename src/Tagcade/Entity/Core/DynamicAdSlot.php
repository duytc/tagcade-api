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
    protected $name;
    protected $expressions;

    protected $native;
    /**
     * @var LibraryDynamicAdSlotInterface
     */
    protected $libraryDynamicAdSlot;
    public function __construct()
    {}


}