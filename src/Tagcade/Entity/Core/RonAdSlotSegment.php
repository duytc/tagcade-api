<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\RonAdSlotSegment as RonAdSlotSegmentModel;

class RonAdSlotSegment extends RonAdSlotSegmentModel
{
    protected $id;
    protected $ronAdSlot;
    protected $segment;
    protected $createdAt;
    protected $deletedAt;

    public function __construct()
    {}
}