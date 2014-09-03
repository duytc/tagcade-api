<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdSlot as AdSlotModel;

class AdSlot extends AdSlotModel
{
    protected $id;
    protected $site;
    protected $name;
    protected $width;
    protected $height;

    public function __construct()
    {}
}