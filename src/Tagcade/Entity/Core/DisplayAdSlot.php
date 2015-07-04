<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayAdSlot as DisplayAdSlotModel;

class DisplayAdSlot extends DisplayAdSlotModel
{
    protected $id;
    protected $site;
    protected $name;
    protected $width;
    protected $height;
    protected $deletedAt;

    public function __construct()
    {}
}