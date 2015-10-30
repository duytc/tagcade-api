<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Segment as SegmentModel;

class Segment extends SegmentModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $ronAdSlotSegments;
    protected $createdAt;
    protected $deletedAt;

    public function __construct()
    {}
}