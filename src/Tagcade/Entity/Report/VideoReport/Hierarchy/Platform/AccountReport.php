<?php

namespace Tagcade\Entity\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReport as VideoAccountReportModel;

class AccountReport extends VideoAccountReportModel
{
    protected $id;
    protected $date;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $requestFillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $billedAmount;
    protected $billedRate;
    protected $blocks;
    protected $adTagRequests;
    protected $adTagBids;
    protected $adTagErrors;
    protected $publisher;
    protected $estDemandRevenue;
    protected $estSupplyCost;
    protected $netRevenue;
} 