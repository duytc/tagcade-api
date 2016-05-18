<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\AdTag as AdTagModel;

class AdTag extends AdTagModel
{
    protected $id;
    protected $adSlot;
    protected $position;
    protected $active;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $frequencyCap;
    protected $rotation;
    protected $refId;
    protected $libraryAdTag;

    protected $impressionCap;
    protected $networkOpportunityCap;

    public function __construct()
    {
    }

}