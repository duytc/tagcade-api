<?php

namespace Tagcade\Entity\Report\RtbReport;

use Tagcade\Model\Report\RtbReport\Winner as WinnerModel;

/**
 * Winner
 */
class Winner extends WinnerModel
{
    protected $id;
    protected $bidRequestId;
    protected $impId;
    protected $tagId;
    protected $price;
    protected $adm;
    protected $dspId;
    protected $verified;
    protected $date;
}

