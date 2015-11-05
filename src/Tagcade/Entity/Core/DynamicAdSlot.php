<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DynamicAdSlot as DynamicAdSlotModel;

class DynamicAdSlot extends DynamicAdSlotModel
{
    protected $id;
    protected $site;
    protected $expressions;
    protected $defaultAdSlot;

    public function __construct()
    {
        parent::__construct();
    }
}