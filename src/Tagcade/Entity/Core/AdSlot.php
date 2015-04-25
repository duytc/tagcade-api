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
    protected $deletedAt;

    protected $enableVariable;
    protected $variableDescriptor;
    protected $expressions;

    public function __construct()
    {}
}