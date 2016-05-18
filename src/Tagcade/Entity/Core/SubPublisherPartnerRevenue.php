<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\SubPublisherPartnerRevenue as SubPublisherPartnerRevenueModel;

class SubPublisherPartnerRevenue extends SubPublisherPartnerRevenueModel
{
    protected $id;
    protected $subPublisher;
    protected $adNetworkPartner;
    protected $revenueOption;
    protected $revenueValue;

    /**
     * this constructor will be called by FormType, must be used to call parent to set default values
     */
    public function __construct()
    {
        parent::__construct();
    }
}