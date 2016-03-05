<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\RonAdSlot as RonAdSlotModel;

class RonAdSlot extends RonAdSlotModel
{
    protected $id;
    protected $libraryAdSlot;
    protected $ronAdSlotSegments;
    protected $rtbStatus;
    protected $floorPrice;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }
}