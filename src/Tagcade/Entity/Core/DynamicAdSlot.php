<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DynamicAdSlot as DynamicAdSlotModel;

class DynamicAdSlot extends DynamicAdSlotModel
{
    protected $id;
    protected $site;
    protected $name;

    protected $defaultAdSlot;
    protected $expressions;

    public function __construct()
    {}
}