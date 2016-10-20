<?php

namespace Tagcade\Entity\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\WaterfallTagReport as WaterfallTagReportModel;
class WaterfallTagReport extends WaterfallTagReportModel {

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
    protected $blocks;

    protected $adTagRequests;
    protected $adTagBids;
    protected $adTagErrors;
    protected $billedAmount;
    protected $billedRate;
    protected $customRate;

    protected $videoWaterfallTag;
} 