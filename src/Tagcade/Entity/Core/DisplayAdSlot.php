<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\DisplayAdSlot as DisplayAdSlotModel;

class DisplayAdSlot extends DisplayAdSlotModel
{
    protected $id;
    protected $site;
    protected $rtbStatus;
    protected $floorPrice;
    protected $deletedAt;
    protected $headerBiddingPrice;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }
}