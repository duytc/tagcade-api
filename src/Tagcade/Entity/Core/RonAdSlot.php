<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\RonAdSlot as RonAdSlotModel;

class RonAdSlot extends RonAdSlotModel
{
    protected $id;
    protected $libraryAdSlot;
    protected $ronAdSlotSegments;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    public function __construct()
    {
    }
}